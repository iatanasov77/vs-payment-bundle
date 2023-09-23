<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PaidServiceCategoriesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'paid_service_categories';
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
                ->scalarNode( 'taxon_code' )->end()
        ;
    }
}
