<?php namespace Vankosoft\PaymentBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

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
    
    /** @var UserInterface|null */
    private $user;
    
    /** @var RepositoryInterface */
    private $pricingPlanSubscriptionRepository;
    
    /** @var Factory */
    private $pricingPlanSubscriptionFactory;
    
    /** @vvar OrderFactory */
    private $orderFactory;
    
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ManagerRegistry $doctrine,
        RepositoryInterface $pricingPlanSubscriptionRepository,
        Factory $pricingPlanSubscriptionFactory,
        OrderFactory $orderFactory,
        StripeApi $stripeApi
    ) {
        $this->doctrine                             = $doctrine;
        $this->pricingPlanSubscriptionRepository    = $pricingPlanSubscriptionRepository;
        $this->pricingPlanSubscriptionFactory       = $pricingPlanSubscriptionFactory;
        $this->orderFactory                         = $orderFactory;
        $this->stripeApi                            = $stripeApi;
        
        $token          = $tokenStorage->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
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
        $pricingPlan    = $event->getPricingPlan();
        $previousSubscription   = $this->user->getActivePricingPlanSubscriptionByService(
            $pricingPlan->getPaidService()->getPayedService()
        );
        
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $this->user );
        $subscription->setPricingPlan( $pricingPlan );
        $subscription->setRecurringPayment( $event->getSetRecurringPayments() );
        
        $startDate      = $previousSubscription ? $previousSubscription->getExpiresAt() : new \DateTime();
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
        $previousSubscription   = $this->user->getActivePricingPlanSubscriptionByService(
            $subscription->getPricingPlan()->getPaidService()->getPayedService()
        );
        if ( $previousSubscription ) {
            $previousSubscription->setActive( false );
            $this->doctrine->getManager()->persist( $previousSubscription );
        }
        
        $subscription->setActive( true );
        
        if ( $subscription->isRecurringPayment() ) {
            $paymentData    = $payment->getDetails();
            $gtAttributes   = $subscription->getGatewayAttributes();
            $gtAttributes   = $gtAttributes ?: [];
            
            $paymentFactory = $payment->getOrder()->getPaymentMethod()->getGateway()->getFactoryName();
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
        $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]       = isset( $paymentData['local']['customer'] ) ?
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