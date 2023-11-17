<?php namespace Vankosoft\PaymentBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;
use Vankosoft\PaymentBundle\EventSubscriber\Event\CreateSubscriptionEvent;

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
            CreateSubscriptionEvent::NAME       => 'createSubscription',
            SubscriptionsPaymentDoneEvent::NAME => 'setSubscriptionsPayment',
        ];
    }

    public function createSubscription( CreateSubscriptionEvent $event )
    {
        $em             = $this->doctrine->getManager();
        $subscription   = $this->pricingPlanSubscriptionFactory->createNew();
        
        $subscription->setUser( $this->user );
        $subscription->setPricingPlan( $event->getPricingPlan() );
        $subscription->setCode( $event->getPricingPlan()->getSubscriptionCode() );
        
        $em->persist( $subscription );
        $em->flush();
    }
    
    public function setSubscriptionsPayment( SubscriptionsPaymentDoneEvent $event )
    {
        $em = $this->doctrine->getManager();
        
        foreach ( $event->getSubscriptions() as $subscription ) {
            $this->setSubscriptionPaid( $subscription );
        }
        
        $em->flush();
    }
    
    private function setSubscriptionPaid( PricingPlanSubscriptionInterface $subscription )
    {
        $em = $this->doctrine->getManager();
        
        $startDate  = $subscription->getExpiresAt() ?: new \DateTime();
        $endDate    = clone $startDate;
        $endDate    = $endDate->add( $subscription->getPricingPlan()->getSubscriptionPeriod() );
        
        $subscription->setExpiresAt( $endDate );
        
        $em->persist( $subscription );
    }
}