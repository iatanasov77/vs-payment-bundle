<?php namespace Vankosoft\PaymentBundle\Controller\PricingPlans;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\TaxonomyHelperTrait;

class PricingPlanCategoryController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonomy       = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
            $this->getParameter( 'vs_payment.product_category.taxonomy_code' )
        );
        
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        return [
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
            'translations'  => $translations,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale     = $form['currentLocale']->getData();
        $this->get( 'vs_application.slug_generator' )->setLocaleCode( $translatableLocale );
        
        $categoryName           = $form['name']->getData();
        $parentCategory         = isset( $_POST['product_category_form']['parent'] ) ?
                                    $this->get( 'vs_payment.repository.product_category' )->findByTaxonId( $_POST['product_category_form']['parent'] ) :
                                    null;
        
        if ( $entity->getTaxon() ) {
            $entityTaxon    = $entity->getTaxon();
            
            $entityTaxon->getTranslation( $translatableLocale );
            $entityTaxon->setCurrentLocale( $translatableLocale );
            $request->setLocale( $translatableLocale );
            if ( ! in_array( $translatableLocale, $entityTaxon->getExistingTranslations() ) ) {
                $taxonTranslation   = $this->createTranslation( $entityTaxon, $translatableLocale, $categoryName );
                
                $entityTaxon->addTranslation( $taxonTranslation );
            } else {
                $taxonTranslation   = $entityTaxon->getTranslation( $translatableLocale );

                $taxonTranslation->setName( $categoryName );
                $taxonTranslation->setSlug( $this->get( 'vs_application.slug_generator' )->generate( $categoryName ) );
            }
            
            if ( $parentCategory ) {
                $entityTaxon->setParent( $parentCategory->getTaxon() );
            }
            
            $entity->setParent( $parentCategory );
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                                        $this->getParameter( 'vs_payment.product_category.taxonomy_code' )
                                    );
            $newTaxon   = $this->createTaxon(
                $categoryName,
                $translatableLocale,
                $parentCategory ? $parentCategory->getTaxon() : null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $newTaxon );
            $entity->setParent( $parentCategory );
        }
    }
}
