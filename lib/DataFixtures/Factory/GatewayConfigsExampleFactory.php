<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\GatewayConfigInterface;

final class GatewayConfigsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $gatewayConfigsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $currenciesRepository;
    
    public function __construct(
        FactoryInterface $gatewayConfigsFactory,
        RepositoryInterface $currenciesRepository
    ) {
        $this->gatewayConfigs   = $currenciesFactory;
        
        $this->optionsResolver  = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->currenciesRepository = $currenciesRepository;
    }
    
    public function create( array $options = [] ): GatewayConfigInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $currency   = $this->currenciesRepository->findOneBy( ['code' => $options['currency']] );
        
        $entity     = $this->gatewayConfigsFactory->createNew();
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setGatewayName( $options['gateway_name'] );
        $entity->setFactoryName( $options['factory_name'] );
        $entity->setUseSandbox( $options['use_sandbox'] );
        $entity->setConfig( $options['config'] );
        $entity->setSandboxConfig( $options['sandbox_config'] );
        $entity->setCurrency( $currency );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'gateway_name', null )
            ->setAllowedTypes( 'gateway_name', ['string'] )
            
            ->setDefault( 'factory_name', null )
            ->setAllowedTypes( 'factory_name', ['string'] )
            
            ->setDefault( 'use_sandbox', null )
            ->setAllowedTypes( 'use_sandbox', ['bool'] )
            
            ->setDefault( 'config', null )
            ->setAllowedTypes( 'config', ['array'] )
            
            ->setDefault( 'sandbox_config', null )
            ->setAllowedTypes( 'sandbox_config', ['array'] )
            
            ->setDefault( 'currency', null )
            ->setAllowedTypes( 'currency', ['string'] )
        ;
    }
}
