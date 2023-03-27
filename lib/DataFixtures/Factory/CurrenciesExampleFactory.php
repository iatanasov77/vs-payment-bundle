<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;

final class CurrenciesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $currenciesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    public function __construct(
        FactoryInterface $currenciesFactory
    ) {
        $this->currenciesFactory    = $currenciesFactory;
            
        $this->optionsResolver      = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
    }
    
    public function create( array $options = [] ): CurrencyInterface
    {
        $options    = $this->optionsResolver->resolve( $options );

        $entity     = $this->currenciesFactory->createNew();
        
        $entity->setCode( $options['code'] );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'code', null )
            ->setAllowedTypes( 'code', ['string'] )
        ;
    }
}
