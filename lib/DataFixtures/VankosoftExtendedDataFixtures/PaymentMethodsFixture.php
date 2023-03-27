<?php namespace Vankosoft\PaymentBundle\DataFixtures\VankosoftExtendedDataFixtures;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\AbstractResourceFixture;

final class PaymentMethodsFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'payment_methods';
    }
    
    protected function configureResourceNode( ArrayNodeDefinition $resourceNode ): void
    {
        $resourceNode
            ->children()
                ->scalarNode( 'gateway' )->end()
                ->scalarNode( 'name' )->end()
                ->booleanNode( 'enabled' )->defaultTrue()->end()
        ;
    }
}
