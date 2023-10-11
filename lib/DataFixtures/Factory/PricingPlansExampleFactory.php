<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

final class PricingPlansExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $pricingPlansFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $categoriesRepository;
    
    /** @var RepositoryInterface */
    private $currenciesRepository;
    
    /** @var RepositoryInterface */
    private $paidServicesPeriodRepository;
    
    public function __construct(
        FactoryInterface $pricingPlansFactory,
        RepositoryInterface $categoriesRepository,
        RepositoryInterface $currenciesRepository,
        RepositoryInterface $paidServicesPeriodRepository
    ) {
        $this->pricingPlansFactory          = $pricingPlansFactory;
        
        $this->optionsResolver              = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->categoriesRepository         = $categoriesRepository;
        $this->currenciesRepository         = $currenciesRepository;
        $this->paidServicesPeriodRepository = $paidServicesPeriodRepository;
    }
    
    public function create( array $options = [] ): PricingPlanInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $entity     = $this->pricingPlansFactory->createNew();
        $category   = $this->categoriesRepository->findByTaxonCode( $options['category_code'] );
        $currency   = $this->currenciesRepository->findOneBy( ['code' => $options['currencyCode']] );
        
        $entity->setCategory( $category );
        $entity->setTranslatableLocale( $options['locale'] );
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setEnabled( $options['active'] );
        $entity->setPremium( $options['premium'] );
        $entity->setPrice( $options['price'] );
        $entity->setCurrency( $currency );
        $entity->setSubscriptionPriority( $options['subscription_priority'] );
        
        $period = $this->paidServicesPeriodRepository->findOneBy( ['paidServicePeriodCode' => $options['paidServicePeriodCode']] );
        $entity->setPaidService( $period );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'category_code', null )
            ->setAllowedTypes( 'category_code', ['string'] )
            
            ->setDefault( 'locale', null )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'active', true )
            ->setAllowedTypes( 'active', ['bool'] )
            
            ->setDefault( 'premium', false )
            ->setAllowedTypes( 'premium', ['bool'] )
            
            ->setDefault( 'recurringPayment', false )
            ->setAllowedTypes( 'recurringPayment', ['bool'] )
            
            ->setDefault( 'price', null )
            ->setAllowedTypes( 'price', ['float'] )
            
            ->setDefault( 'subscription_priority', 1 )
            ->setAllowedTypes( 'subscription_priority', ['int'] )
            
            ->setDefault( 'currencyCode', null )
            
            ->setDefault( 'paidServicePeriodCode', null )
            ->setAllowedTypes( 'paidServicePeriodCode', ['string'] )
        ;
    }
}
