<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class ProductsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'products';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                
                ->scalarNode( 'name' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'category_code' )->end()
                ->scalarNode( 'locale' )->end()
                ->booleanNode( 'published' )->defaultTrue()->end()
                ->scalarNode( 'price' )->end()
                ->scalarNode( 'currency' )->end()
                ->arrayNode( 'pictures' )
                    ->children()
                        ->scalarNode( 'file' )->end()
                    ->end()
                ->end()
        ;
    }
}
