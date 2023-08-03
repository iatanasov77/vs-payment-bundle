<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PricingPlansFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'pricing_plans';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'category_code' )->end()
                ->scalarNode( 'locale' )->end()
                ->booleanNode( 'active' )->defaultTrue()->end()
                ->booleanNode( 'premium' )->defaultFalse()->end()
                ->scalarNode( 'paid_service_period' )->end()
        ;
    }
}
