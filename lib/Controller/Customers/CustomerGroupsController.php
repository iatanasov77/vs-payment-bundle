<?php namespace Vankosoft\PaymentBundle\Controller\Customers;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\ApplicationBundle\Controller\TaxonomyHelperTrait;

class CustomerGroupsController extends AbstractCrudController
{
    use TaxonomyHelperTrait;
    
    protected function customData( Request $request, $entity = null ): array
    {
        $taxonomy       = $this->getTaxonomy( 'vs_payment.customer_groups.taxonomy_code' );
        
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations( false ) : [];
        if ( $entity && $entity->getTaxon() ) {
            $entity->getTaxon()->setCurrentLocale( $request->getLocale() );
        }
        
        return [
            'taxonomyId'    => $taxonomy ? $taxonomy->getId() : 0,
            'translations'  => $translations,
            'items'         => $this->getRepository()->findAll(),
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $translatableLocale     = $form['currentLocale']->getData();
        $this->get( 'vs_application.slug_generator' )->setLocaleCode( $translatableLocale );
        
        $groupName  = $form['name']->getData();
        
        if ( $entity->getTaxon() ) {
            $entityTaxon    = $entity->getTaxon();
            
            $entityTaxon->getTranslation( $translatableLocale );
            $entityTaxon->setCurrentLocale( $translatableLocale );
            $request->setLocale( $translatableLocale );
            if ( ! in_array( $translatableLocale, $entityTaxon->getExistingTranslations() ) ) {
                $taxonTranslation   = $this->createTranslation( $entityTaxon, $translatableLocale, $groupName );
                
                $entityTaxon->addTranslation( $taxonTranslation );
            } else {
                $taxonTranslation   = $entityTaxon->getTranslation( $translatableLocale );
                
                $taxonTranslation->setName( $groupName );
                $taxonTranslation->setSlug( $this->get( 'vs_application.slug_generator' )->generate( $groupName ) );
            }
        } else {
            /*
             * @WORKAROUND Create Taxon If not exists
             */
            $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                $this->getParameter( 'vs_catalog.pricing_plan_category.taxonomy_code' )
                );
            $newTaxon   = $this->createTaxon(
                $groupName,
                $translatableLocale,
                null,
                $taxonomy->getId()
            );
            
            $entity->setTaxon( $newTaxon );
        }
        
        $em = $this->get( 'doctrine.orm.entity_manager' );
        $groupCustomers = $form['customers']->getData();
        foreach( $groupCustomers as $customer ) {
            $entity->addCustomer( $customer );
            
            $em->persist( $customer );
            $em->flush();
        }
    }
}