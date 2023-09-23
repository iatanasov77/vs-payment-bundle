<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;
use Vankosoft\ApplicationBundle\Component\SlugGenerator;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;

final class PricingPlanCategoriesExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $pricingPlanCategoriesFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $taxonomyRepository;
    
    /** @var FactoryInterface */
    private $taxonFactory;
    
    /** @var SlugGenerator */
    private $slugGenerator;
    
    public function __construct(
        FactoryInterface $pricingPlanCategoriesFactory,
        
        RepositoryInterface $taxonomyRepository,
        FactoryInterface $taxonFactory,
        SlugGenerator $slugGenerator
    ) {
        $this->pricingPlanCategoriesFactory = $pricingPlanCategoriesFactory;
        
        $this->optionsResolver              = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->taxonomyRepository           = $taxonomyRepository;
        $this->taxonFactory                 = $taxonFactory;
        $this->slugGenerator                = $slugGenerator;
    }
    
    public function create( array $options = [] ): PricingPlanCategoryInterface
    {
        $options                    = $this->optionsResolver->resolve( $options );
        
        $taxonomyRootTaxonEntity    = $this->taxonomyRepository->findByCode( $options['taxonomy_code'] )->getRootTaxon();
        $entity                     = $this->pricingPlanCategoriesFactory->createNew();
        
        $taxonEntity                = $this->taxonFactory->createNew();
        
        //$slug                       = $this->slugGenerator->generate( $options['title'] );
        $slug                       = $options['taxon_code'];
        
        $taxonEntity->setCurrentLocale( $options['locale'] );
        $taxonEntity->setCode( $slug );
        $taxonEntity->getTranslation()->setName( $options['title'] );
        $taxonEntity->getTranslation()->setDescription( $options['description'] );
        $taxonEntity->getTranslation()->setSlug( $slug );
        $taxonEntity->getTranslation()->setTranslatable( $taxonEntity );
        
        $taxonEntity->setParent( $taxonomyRootTaxonEntity );
        $entity->setTaxon( $taxonEntity );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'parent', null )
            ->setAllowedTypes( 'parent', ['string', 'null'] )
        
            ->setDefault( 'title', null )
            ->setAllowedTypes( 'title', ['string'] )
            
            ->setDefault( 'description', null )
            ->setAllowedTypes( 'description', ['string'] )
            
            ->setDefault( 'locale', 'en_US' )
            ->setAllowedTypes( 'locale', ['string'] )
            
            ->setDefault( 'taxonomy_code', null )
            ->setAllowedTypes( 'taxonomy_code', ['string'] )
            
            ->setDefault( 'taxon_code', null )
            ->setAllowedTypes( 'taxon_code', ['string'] )
        ;
    }
}
