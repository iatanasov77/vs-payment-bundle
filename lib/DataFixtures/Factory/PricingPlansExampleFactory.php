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
    
    public function __construct(
        FactoryInterface $pricingPlansFactory,
        RepositoryInterface $categoriesRepository,
        RepositoryInterface $paidServicesPeriodRepository
    ) {
        $this->pricingPlansFactory          = $pricingPlansFactory;
        
        $this->optionsResolver              = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->categoriesRepository         = $categoriesRepository;
        $this->paidServicesPeriodRepository = $paidServicesPeriodRepository;
    }
    
    public function create( array $options = [] ): PricingPlanInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $entity     = $this->pricingPlansFactory->createNew();
        $category   = $this->categoriesRepository->findByTaxonCode( $options['category_code'] );
        
        $entity->setCategory( $category );
        $entity->setTranslatableLocale( $options['locale'] );
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setEnabled( $options['active'] );
        $entity->setPremium( $options['premium'] );
        
        $period = $this->paidServicesPeriodRepository->findOneBy( ['paidServicePeriodCode' => $options['paid_service_period']] );
        $entity->setPaidServicePeriod( $period );
        
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
            
            ->setDefault( 'active', null )
            ->setAllowedTypes( 'active', ['bool'] )
            
            ->setDefault( 'premium', null )
            ->setAllowedTypes( 'premium', ['bool'] )
            
            ->setDefault( 'paid_service_period', null )
        ;
    }
}
