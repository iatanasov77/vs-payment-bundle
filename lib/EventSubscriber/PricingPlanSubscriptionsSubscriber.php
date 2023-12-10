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
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ManagerRegistry $doctrine,
        RepositoryInterface $pricingPlanSubscriptionRepository,
        Factory $pricingPlanSubscriptionFactory,
        OrderFactory $orderFactory
    ) {
        $this->doctrine                             = $doctrine;
        $this->pricingPlanSubscriptionRepository    = $pricingPlanSubscriptionRepository;
        $this->pricingPlanSubscriptionFactory       = $pricingPlanSubscriptionFactory;
        $this->orderFactory                         = $orderFactory;
        
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
        $em             = $this->doctrine->getManager();
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $this->user );
        $subscription->setPricingPlan( $event->getPricingPlan() );
        $subscription->setRecurringPayment( $event->getSetRecurringPayments() );
        
        $em->persist( $subscription );
        $em->flush();
    }
    
    public function createNewUserSubscription( CreateNewUserSubscriptionEvent $event )
    {
        $em             = $this->doctrine->getManager();
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $event->getUser() );
        $subscription->setPricingPlan( $event->getPricingPlan() );
        
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
        $em = $this->doctrine->getManager();
        
        $startDate  = $subscription->getExpiresAt() ?: new \DateTime();
        $endDate    = clone $startDate;
        $endDate    = $endDate->add( $subscription->getPricingPlan()->getSubscriptionPeriod() );
        
        $subscription->setExpiresAt( $endDate );
        
        if ( $subscription->isRecurringPayment() ) {
            $paymentData    = $payment->getDetails();
            $gtAttributes   = $subscription->getGatewayAttributes();
            $gtAttributes   = $gtAttributes ?: [];
            
            $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY]    = isset( $paymentData['local']['customer'] ) ?
                                                                    $paymentData['local']['customer']['id'] : null;
            $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]       = isset( $paymentData['local']['customer'] ) ?
                                                                    $paymentData['local']['customer']['plan'] : null;
            $subscription->setGatewayAttributes( $gtAttributes );
        }
        
        $em->persist( $subscription );
    }
}