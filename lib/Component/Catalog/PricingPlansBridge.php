<?php namespace Vankosoft\PaymentBundle\Component\Catalog;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Vankosoft\ApplicationBundle\Component\Application\Project;
use Vankosoft\CatalogBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;

final class PricingPlansBridge implements CatalogBridgeInterface
{
    /** @var ContainerInterface $container */
    private $container;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container    = $container;
    }
    
    public function getFactory()
    {
        if ( $this->container->has( 'vs_catalog.factory.pricing_plan' ) ) {
            return $this->container->get( 'vs_catalog.factory.pricing_plan' );
        }
        
        return null;
    }
    
    public function getRepository()
    {
        if ( $this->container->has( 'vs_catalog.repository.pricing_plan' ) ) {
            return $this->container->get( 'vs_catalog.repository.pricing_plan' );
        }
        
        return null;
    }
    
    public function getModelClass()
    {
        if ( $this->container->hasParameter( 'vs_catalog.model.pricing_plan.class' ) ) {
            return $this->container->getParameter( 'vs_catalog.model.pricing_plan.class' );
        }
        
        return null;
    }
}