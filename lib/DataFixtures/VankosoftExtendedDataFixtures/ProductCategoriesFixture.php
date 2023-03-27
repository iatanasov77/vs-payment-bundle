<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class ProductCategoriesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'product_categories';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'parent' )->end()
                ->scalarNode( 'locale' )->end()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'taxonomy_code' )->end()
        ;
    }
}
