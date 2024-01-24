<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class VSPaymentExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    use PrependPayumTrait;
    //use PrependDoctrineMigrationsTrait;
    
    /**
     * {@inheritDoc}
     */
    public function load( array $configs, ContainerBuilder $container )
    {
        $config = $this->processConfiguration( $this->getConfiguration( [], $container ), $configs );
        
        $loader = new Loader\YamlFileLoader( $container, new FileLocator( __DIR__.'/../Resources/config' ) );
        $loader->load( 'services.yml' );
        
        // Register resources
        $this->registerResources( 'vs_payment', $config['orm_driver'], $config['resources'], $container );
        
        $this->prepend( $container );
    }
    
    public function prepend( ContainerBuilder $container ): void
    {
        $config = $container->getExtensionConfig( $this->getAlias() );
        $config = $this->processConfiguration( $this->getConfiguration( [], $container ), $config );
        
        $this->prependPayum( $container, $config );
        //$this->prependDoctrineMigrations( $container );
    }
    
    private function debugExtensionConfig( ContainerBuilder $container, string $extension )
    {
        $debugArray = $container->getExtensionConfig( $extension );
        
        $fileLocator = new FileLocator( $container->getParameter( 'kernel.project_dir' ) );
        //$debugArray['MigrationsPath'] = $fileLocator->locate("@VSApplicationBundle/DoctrineMigrations");
        $debugArray['MigrationsPath'] = $fileLocator->locate("@VSPaymentBundle/DoctrineMigrations");
        
        return $debugArray;
    }
}
