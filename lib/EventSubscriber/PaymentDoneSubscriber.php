<?php namespace Vankosoft\PaymentBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\UsersBundle\Model\UserInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class PaymentDoneSubscriber implements EventSubscriberInterface
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
            PaymentDoneEvent::NAME    => 'createSubscription',
        ];
    }

    public function createSubscription( PaymentDoneEvent $event )
    {
        switch ( $event->getAction() ) {
            case PaymentDoneEvent::ACTION_CREATE_SUBSCRIPTION:
                throw new \Exception( 'Action Unimplemented !' );
                break;
            case PaymentDoneEvent::ACTION_PAY_SUBSCRIPTION:
                $this->_setSubscriptionPayment( $event->getResource() );
                break;
            default:
                throw new \Exception( 'Unknown Event Action !' );
        }
    }
    
    protected function _setSubscriptionPayment( $subscription )
    {
//         $order          = $this->orderFactory->getShoppingCart();
//         $subscription   = $order->getSubscription();
        $em             = $this->doctrine->getManager();
        
        if ( $subscription ) {
            $subscription->setPaid( true );
            $em->persist( $subscription );
            $em->flush();
        }
    }
}