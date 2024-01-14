<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;
use Payum\Core\GatewayFactoryInterface;

class TelephoneCallGatewayFactoryBuilder extends GatewayFactoryBuilder implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @return GatewayFactoryInterface
     */
    public function build( array $defaultConfig, GatewayFactoryInterface $coreGatewayFactory )
    {
        $gatewayFactoryClass    = $this->gatewayFactoryClass;
        $gatewayFactory         = new $gatewayFactoryClass( $defaultConfig, $coreGatewayFactory );
        $gatewayFactory->setContainer( $this->container );

        return $gatewayFactory;
    }
}