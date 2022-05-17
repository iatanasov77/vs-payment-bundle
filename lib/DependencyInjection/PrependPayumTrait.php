<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependPayumTrait
{
    private function prependPayum( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'payum' ) ) {
            return;
        }
        //$this->debug( $container );
        
        $vsPaymentResources = \array_pop( $container->getExtensionConfig( 'vs_payment' ) )['resources'];
        var_dump( $vsPaymentResources['payment']["classes"]["model"] ); die;
        $payumConfig        = $container->getExtensionConfig( 'payum' );
        $container->prependExtensionConfig( 'payum', [
            'storages'  => \array_merge( \array_pop( $payumConfig )['storages'] ?? [], [
                $vsPaymentResources['payment']["classes"]["model"] => $this->getMigrationsDirectory(),
            ]),
            'dynamic_gateways' => \array_merge( \array_pop( $payumConfig )['dynamic_gateways'] ?? [], [
                $this->getMigrationsNamespace() => $this->getMigrationsDirectory(),
            ]),
        ]);
    }
    
    private function debug( ContainerBuilder $container )
    {
        echo '<pre>';
        //var_dump( $container->getParameter( 'vs_payment.model.gateway_config.class' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'vs_payment' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'payum' ) ); die;
    }
}
