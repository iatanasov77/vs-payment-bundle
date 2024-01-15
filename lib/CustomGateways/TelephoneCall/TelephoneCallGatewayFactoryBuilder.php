<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Payum\Core\GatewayFactoryInterface;

class TelephoneCallGatewayFactoryBuilder extends GatewayFactoryBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * 
     * {@inheritDoc}
     * @see \Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder::__invoke()
     */
    public function __invoke()
    {
        $gatewayFactory = call_user_func_array([$this, 'build'], func_get_args());
        $gatewayFactory->setContainer( $this->container );
        
        return $gatewayFactory;
    }
}