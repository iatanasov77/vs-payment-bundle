<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Psr\Container\ContainerInterface;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Payum\Core\GatewayFactoryInterface;

class TelephoneCallGatewayFactoryBuilder extends GatewayFactoryBuilder
{
    /** @var ContainerInterface */
    private $container;
    
    /**
     * @param string $gatewayFactoryClass
     * @param ContainerInterface $container
     */
    public function __construct( $gatewayFactoryClass, ContainerInterface $container )
    {
        parent::__construct( $gatewayFactoryClass );
        $this->container = $container;
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder::__invoke()
     */
    public function __invoke()
    {
        $gatewayFactory = call_user_func_array( [$this, 'build'], func_get_args() );
        $gatewayFactory->setContainer( $this->container );
        
        return $gatewayFactory;
    }
}