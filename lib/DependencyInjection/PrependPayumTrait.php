<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependPayumTrait
{
    private function prependPayum( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'payum' ) ) {
            return;
        }
        
        echo '<pre>';
        var_dump( $container->getParameter( 'vs_payment.model.gateway_config.class' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'payum' ) ); die;
        
        
        if ( $container->hasParameter( 'vs_payment.model.gateway_config.class' ) ) {
            
        }
                
        $payumConfig = $container->getExtensionConfig( 'payum' );
        
        $container->prependExtensionConfig( 'payum', [
            'migrations_paths' => \array_merge( \array_pop( $doctrineConfig )['migrations_paths'] ?? [], [
                $this->getMigrationsNamespace() => $this->getMigrationsDirectory(),
            ]),
        ]);
    }
}
