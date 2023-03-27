<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class GatewayConfigsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'gateway_configs';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'title' )->end()
                ->scalarNode( 'description' )->end()
                ->scalarNode( 'gateway_name' )->end()
                ->scalarNode( 'factory_name' )->end()
                ->booleanNode( 'use_sandbox' )->defaultTrue()->end()
                ->arrayNode( 'config' )->end()
                ->arrayNode( 'sandbox_config' )->end()
                ->scalarNode( 'currency' )->end()
        ;
    }
}
