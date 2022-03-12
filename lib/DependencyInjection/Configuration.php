<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder    = new TreeBuilder( 'vs_payment' );
        $rootNode       = $treeBuilder->getRootNode();
        
        $rootNode
            ->children()
                ->scalarNode( 'orm_driver' )
                    ->defaultValue( SyliusResourceBundle::DRIVER_DOCTRINE_ORM )->cannotBeEmpty()
                ->end()
        //             ->arrayNode('payment_accounts')->isRequired()
        //             ->prototype('variable')
        //             ->treatNullLike(array())
        //         ;
        
        //         $rootNode->children()
        //             ->arrayNode('payment_methods')->isRequired()
        //                 ->prototype('variable')
        //                 ->treatNullLike(array())
            ->end()
        ;
        $this->addResourcesSection( $rootNode );

        return $treeBuilder;
    }
    
    private function addResourcesSection( ArrayNodeDefinition $node ): void
    {
        
    }
}
