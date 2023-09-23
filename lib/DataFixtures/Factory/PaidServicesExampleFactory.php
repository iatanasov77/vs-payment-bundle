<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceInterface;

final class PaidServicesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $paidServicesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $categoriesRepository;
    
    /** @var FactoryInterface */
    private $paidServicesSubscriptionPeriodFactory;
    
    public function __construct(
        FactoryInterface $paidServicesFactory,
        RepositoryInterface $categoriesRepository,
        FactoryInterface $paidServicesSubscriptionPeriodFactory
    ) {
        $this->paidServicesFactory                      = $paidServicesFactory;
        
        $this->optionsResolver                          = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->categoriesRepository                     = $categoriesRepository;
        $this->paidServicesSubscriptionPeriodFactory    = $paidServicesSubscriptionPeriodFactory;
    }
    
    public function create( array $options = [] ): PayedServiceInterface
    {
        $options    = $this->optionsResolver->resolve( $options );
        
        $entity     = $this->paidServicesFactory->createNew();
        $category   = $this->categoriesRepository->findByTaxonCode( $options['category_code'] );
        
        $entity->setCategory( $category );
        $entity->setTranslatableLocale( $options['locale'] );
        $entity->setTitle( $options['title'] );
        $entity->setDescription( $options['description'] );
        $entity->setEnabled( $options['active'] );
        $entity->setSubscriptionCode( $options['subscription_code'] );
        $entity->setSubscriptionPriority( $options['subscription_priority'] );
        
        if ( isset( $options['periods'] ) && null !== $options['periods'] ) {
            $this->createSubscriptionPeriods( $entity, $options['periods'], $options['locale'] );
        }
        
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
            
            ->setDefault( 'subscription_code', null )
            ->setAllowedTypes( 'subscription_code', ['string'] )
            
            ->setDefault( 'subscription_priority', null )
            ->setAllowedTypes( 'subscription_priority', ['integer'] )
            
            ->setDefault( 'periods', null )
        ;
    }
    
    private function createSubscriptionPeriods( &$entity, array $periods, string $locale )
    {
        foreach( $periods as $sp ) {
            $period        = $this->paidServicesSubscriptionPeriodFactory->createNew();
            
            $entity->setTranslatableLocale( $locale );
            
            $period->setSubscriptionPeriod( $sp['subscriptionPeriod'] );
            $period->setPrice( $sp['price'] );
            $period->setCurrencyCode( $sp['currencyCode'] );
            $period->setTitle( $sp['title'] );
            $period->setPaidServicePeriodCode( $sp['paidServicePeriodCode'] );
            
            $entity->addSubscriptionPeriod( $period );
        }
    }
}