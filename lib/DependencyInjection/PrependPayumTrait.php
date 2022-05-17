<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependPayumTrait
{
    private function prependPayum( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'payum' ) ) {
            return;
        }
            
        if ( $container->hasParameter( 'vankosoft_application.prepend_doctrine_migrations' ) ) {
            var_dump( $container->getExtensionConfig( 'payum' ) ); die;
        }
                
        $payumConfig = $container->getExtensionConfig( 'payum' );
        
        $container->prependExtensionConfig( 'payum', [
            'migrations_paths' => \array_merge( \array_pop( $doctrineConfig )['migrations_paths'] ?? [], [
                $this->getMigrationsNamespace() => $this->getMigrationsDirectory(),
            ]),
        ]);
    }
}
