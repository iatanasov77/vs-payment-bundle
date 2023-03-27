<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Sylius\Component\Currency\Model\ExchangeRateInterface;

final class ExchangeRatesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $exchangeRatesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $currenciesRepository;
    
    public function __construct(
        FactoryInterface $exchangeRatesFactory,
        RepositoryInterface $currenciesRepository
    ) {
        $this->exchangeRatesFactory = $exchangeRatesFactory;
        
        $this->optionsResolver      = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->currenciesRepository = $currenciesRepository;
    }
    
    public function create( array $options = [] ): ExchangeRateInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $sourceCurrency = $this->currenciesRepository->findOneBy( ['code' => $options['source_currency']] );
        $targetCurrency = $this->currenciesRepository->findOneBy( ['code' => $options['target_currency']] );
        
        $entity     = $this->exchangeRatesFactory->createNew();
        $entity->setSourceCurrency( $sourceCurrency );
        $entity->setTargetCurrency( $targetCurrency );
        $entity->setRatio( $options['ratio'] );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'source_currency', null )
            ->setAllowedTypes( 'source_currency', ['string'] )
            
            ->setDefault( 'target_currency', null )
            ->setAllowedTypes( 'target_currency', ['string'] )
            
            ->setDefault( 'ratio', null )
            ->setAllowedTypes( 'ratio', ['float'] )
        ;
    }
}
