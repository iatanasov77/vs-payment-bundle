<?php namespace Vankosoft\PaymentBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Component\Payment\Payment;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;
use Vankosoft\PaymentBundle\EventSubscriber\Event\CreateSubscriptionEvent;
use Vankosoft\PaymentBundle\EventSubscriber\Event\CreateNewUserSubscriptionEvent;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class PricingPlanSubscriptionsSubscriber implements EventSubscriberInterface
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var SecurityBridge */
    private $securityBridge;
    
    /** @var RepositoryInterface */
    private $pricingPlanSubscriptionRepository;
    
    /** @var Factory */
    private $pricingPlanSubscriptionFactory;
    
    /** @vvar OrderFactory */
    private $orderFactory;
    
    /** @var StripeApi */
    private $stripeApi;
    
    /** @var Payment */
    private $vsPayment;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        RepositoryInterface $pricingPlanSubscriptionRepository,
        Factory $pricingPlanSubscriptionFactory,
        OrderFactory $orderFactory,
        StripeApi $stripeApi,
        Payment $vsPayment
    ) {
        $this->doctrine                             = $doctrine;
        $this->securityBridge                       = $securityBridge;
        $this->pricingPlanSubscriptionRepository    = $pricingPlanSubscriptionRepository;
        $this->pricingPlanSubscriptionFactory       = $pricingPlanSubscriptionFactory;
        $this->orderFactory                         = $orderFactory;
        $this->stripeApi                            = $stripeApi;
        $this->vsPayment                            = $vsPayment;
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            CreateSubscriptionEvent::NAME           => 'createSubscription',
            CreateNewUserSubscriptionEvent::NAME    => 'createNewUserSubscription',
            SubscriptionsPaymentDoneEvent::NAME     => 'setSubscriptionsPayment',
        ];
    }

    public function createSubscription( CreateSubscriptionEvent $event )
    {
        $pricingPlan            = $event->getPricingPlan();
        $user                   = $this->securityBridge->getUser();
        $previousSubscription   = $user->getActivePricingPlanSubscriptionByService(
            $pricingPlan->getPaidService()->getPayedService()
        );
        
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $user );
        $subscription->setPricingPlan( $pricingPlan );
        $subscription->setRecurringPayment( $event->getSetRecurringPayments() );
        
        $startDate      = $previousSubscription && $previousSubscription->isPaid() ?
                            $previousSubscription->getExpiresAt() :
                            new \DateTime();
        $expiresDate    = $startDate->add( $pricingPlan->getSubscriptionPeriod() );
        $subscription->setExpiresAt( $expiresDate );
        
        $em             = $this->doctrine->getManager();
        $em->persist( $subscription );
        $em->flush();
    }
    
    public function createNewUserSubscription( CreateNewUserSubscriptionEvent $event )
    {
        $pricingPlan    = $event->getPricingPlan();
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $event->getUser() );
        $subscription->setPricingPlan( $pricingPlan );
        
        $startDate      = new \DateTime();
        $expiresDate    = $startDate->add( $pricingPlan->getSubscriptionPeriod() );
        $subscription->setExpiresAt( $expiresDate );
        
        $em             = $this->doctrine->getManager();
        $em->persist( $subscription );
        $em->flush();
    }
    
    public function setSubscriptionsPayment( SubscriptionsPaymentDoneEvent $event )
    {
        $em = $this->doctrine->getManager();
        
        foreach ( $event->getSubscriptions() as $subscription ) {
            $this->setSubscriptionPaid( $subscription, $event->getPayment() );
        }
        
        $em->flush();
    }
    
    private function setSubscriptionPaid( PricingPlanSubscriptionInterface $subscription, $payment )
    {
        $user                   = $this->securityBridge->getUser();
        $previousSubscription   = $user->getActivePricingPlanSubscriptionByService(
            $subscription->getPricingPlan()->getPaidService()->getPayedService()
        );
        if ( $previousSubscription ) {
            $previousSubscription->setActive( false );
            $this->doctrine->getManager()->persist( $previousSubscription );
        }
        
        $subscription->setActive( true );
        $gateway    = $payment->getOrder()->getPaymentMethod()->getGateway();
        
        if ( $this->vsPayment->isGatewaySupportRecurring( $gateway ) ) {
            $paymentData    = $payment->getDetails();
            $gtAttributes   = $subscription->getGatewayAttributes();
            $gtAttributes   = $gtAttributes ?: [];
            
            $paymentFactory = $gateway->getFactoryName();
            if ( $paymentFactory == 'stripe_checkout' || $paymentFactory == 'stripe_js' ) {
                $this->setStripePaymentAttributes( $subscription, $paymentData );
            }
        }
        
        $this->doctrine->getManager()->persist( $subscription );
    }
    
    private function setStripePaymentAttributes( &$subscription, $paymentData )
    {
        $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY]    = isset( $paymentData['local']['customer'] ) ?
                                                                $paymentData['local']['customer']['id'] : null;
        
        $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]       = isset( $paymentData['local']['customer']['plan'] ) ?
                                                                $paymentData['local']['customer']['plan'] : null;
        
        if ( $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY] && $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY] ) {
            $stripeSubscriptions                                    = $this->stripeApi->getSubscriptions([
                'customer'  => $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY],
                'price'     => $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY],
            ]);
            $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY]    = ! empty( $stripeSubscriptions ) ?
                                                                        $stripeSubscriptions[0]['id'] : null;
        }
            
        $subscription->setGatewayAttributes( $gtAttributes );
    }
}