<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class ProductController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                                    $this->getParameter( 'vs_payment.product_category.taxonomy_code' )
                                );
        $selectedTaxonIds   = []
        if ( $this->classInfo['action'] == 'updateAction' ) {
            foreach ( $entity->getCategories() as $cat ) {
                $selectedTaxonIds[] = $cat->getTaxon()->getId();
            }
        }
        
        return [
            'categories'        => $this->get( 'vs_payment.repository.product_category' )->findAll(),
            'taxonomyId'        => $taxonomy ? $taxonomy->getId() : 0,
            'translations'      => $translations,
            'selectedTaxonIds'  => $selectedTaxonIds,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $categories = new ArrayCollection();
        $pcr        = $this->get( 'vs_payment.repository.product_category' );
        
        $formLocale = $request->request->get( 'locale' );
        $formPost   = $request->request->all( 'product_form' );
        $formTaxon  = $formPost['category_taxon'];
        
        if ( $formLocale ) {
            $entity->setTranslatableLocale( $formLocale );
        }
        
        if ( $formTaxon ) {
            foreach ( $formTaxon as $taxonId ) {
                $category       = $pcr->findOneBy( ['taxon' => $taxonId] );
                if ( $category ) {
                    $categories[]   = $category;
                    $entity->addCategory( $category );
                }
            }
            
            foreach ( $entity->getCategories() as $cat ) {
                if ( ! $categories->contains( $cat ) ) {
                    $entity->removeCategory( $cat );
                }
            }
        }
    }
    
    private function getTranslations()
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        foreach ( $this->getRepository()->findAll() as $product ) {
            $translations[$product->getId()] = array_keys( $transRepo->findTranslations( $product ) );
        }
        
        return $translations;
    }
}
