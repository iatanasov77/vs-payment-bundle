<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class ExchangeRatesFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'exchange_rates';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'source_currency' )->end()
                ->scalarNode( 'target_currency' )->end()
                ->scalarNode( 'ratio' )->end()
        ;
    }
}
