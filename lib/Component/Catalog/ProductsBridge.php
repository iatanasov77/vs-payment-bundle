<?php namespace Vankosoft\PaymentBundle\Component\Catalog;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Vankosoft\ApplicationBundle\Component\Application\Project;

final class ProductsBridge implements CatalogBridgeInterface
{
    /** @var ContainerInterface $container */
    private $container;
    
    public function __construct( ContainerInterface $container )
    {
        $this->container    = $container;
    }
    
    public function getFactory()
    {
        if ( $this->container->has( 'vs_catalog.factory.product' ) ) {
            return $this->container->get( 'vs_catalog.factory.product' );
        }
        
        return null;
    }
    
    public function getRepository()
    {
        if ( $this->container->has( 'vs_catalog.repository.product' ) ) {
            return $this->container->get( 'vs_catalog.repository.product' );
        }
        
        return null;
    }
    
    public function getModelClass()
    {
        if ( $this->container->hasParameter( 'vs_catalog.model.product.class' ) ) {
            return $this->container->getParameter( 'vs_catalog.model.product.class' );
        }
        
        return null;
    }
}