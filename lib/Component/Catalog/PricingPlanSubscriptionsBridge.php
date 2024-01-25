<?php namespace Vankosoft\PaymentBundle\Component\Catalog;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Vankosoft\ApplicationBundle\Component\Application\Project;
use Vankosoft\CatalogBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;

final class PricingPlanSubscriptionsBridge implements CatalogBridgeInterface, CatalogEventThrowerInterface
{
    /** @var ContainerInterface $container */
    private $container;
    
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    
    public function __construct( ContainerInterface $container, EventDispatcherInterface $eventDispatcher )
    {
        $this->container        = $container;
        $this->eventDispatcher  = $eventDispatcher;
    }
    
    public function getFactory()
    {
        if ( $this->container->has( 'vs_catalog.factory.pricing_plan_subscription' ) ) {
            return $this->container->get( 'vs_catalog.factory.pricing_plan_subscription' );
        }
        
        return null;
    }
    
    public function getRepository()
    {
        if ( $this->container->has( 'vs_catalog.repository.pricing_plan_subscription' ) ) {
            return $this->container->get( 'vs_catalog.repository.pricing_plan_subscription' );
        }
        
        return null;
    }
    
    public function getModelClass()
    {
        if ( $this->container->hasParameter( 'vs_catalog.model.pricing_plan_subscription.class' ) ) {
            return $this->container->getParameter( 'vs_catalog.model.pricing_plan_subscription.class' );
        }
        
        return null;
    }
    
    public function triggerSubscriptionsPaymentDone( $subscriptions, $payment ): void
    {
        $projectType    = $this->container->getParameter( 'vs_application.project_type' );
        
        if ( $projectType == Project::PROJECT_TYPE_CATALOG || $projectType == Project::PROJECT_TYPE_EXTENDED ) {
            $this->eventDispatcher->dispatch(
                new SubscriptionsPaymentDoneEvent( $subscriptions, $payment ),
                SubscriptionsPaymentDoneEvent::NAME
            );
        }
    }
}