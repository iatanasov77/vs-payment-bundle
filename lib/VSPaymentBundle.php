<?php namespace Vankosoft\PaymentBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;

use Vankosoft\PaymentBundle\DependencyInjection\Compiler\RegisterRuleCheckersPass;
use Vankosoft\PaymentBundle\DependencyInjection\Compiler\RegisterPromotionActionsPass;

class VSPaymentBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
            SyliusResourceBundle::DRIVER_DOCTRINE_MONGODB_ODM,
        ];
    }
    
    public function build( ContainerBuilder $container ): void
    {
        parent::build( $container );
        
        $mappings = [
            realpath( __DIR__.'/Resources/config/doctrine-mapping' ) => 'Vankosoft\PaymentBundle\Model',
        ];
        
        if ( class_exists( 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass' ) ) {
            $container->addCompilerPass( DoctrineOrmMappingsPass::createXmlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createYamlMappingDriver( $mappings ) );
            //$container->addCompilerPass( DoctrineOrmMappingsPass::createAnnotationMappingDriver( \array_values( $mappings ), \array_keys( $mappings ) ) );
        }
        
        $container->addCompilerPass( new RegisterRuleCheckersPass() );
        $container->addCompilerPass( new RegisterPromotionActionsPass() );
    }
    
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new \Vankosoft\PaymentBundle\DependencyInjection\VSPaymentExtension();
    }
}
