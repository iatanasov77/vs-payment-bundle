<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PaidServicesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'paid_services';
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
                ->scalarNode( 'subscription_code' )->end()
                ->scalarNode( 'subscription_priority' )->end()
                ->arrayNode( 'periods' )
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode( 'subscriptionPeriod' )->end()
                            ->scalarNode( 'price' )->end()
                            ->scalarNode( 'currencyCode' )->end()
                            ->scalarNode( 'title' )->end()
                            ->scalarNode( 'paidServicePeriodCode' )->end()
                        ->end()
                    ->end()
                ->end()
        ;
    }
}