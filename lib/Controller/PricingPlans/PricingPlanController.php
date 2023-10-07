<?php namespace Vankosoft\PaymentBundle\Controller\PricingPlans;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

use Symfony\Component\Intl\Currencies;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PricingPlanController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        
        $taxonomy   = $this->get( 'vs_application.repository.taxonomy' )->findByCode(
                                    $this->getParameter( 'vs_payment.pricing_plan_category.taxonomy_code' )
                                );
        
        $selectedTaxonIds   = [];
        if ( $this->classInfo['action'] == 'updateAction' ) {
            $selectedTaxonIds[] = $entity->getCategory()->getTaxon()->getId();
        }
        
        $currencies = [];
        if ( $this->resources ) {
            foreach ( $this->resources as $plan ) {
                $currencies[$plan->getCurrencyCode()]   = [
                    'symbol'    => Currencies::getSymbol( $plan->getCurrencyCode() ),
                    'name'      => Currencies::getName( $plan->getCurrencyCode() ),
                ];
            }
        }
        
        return [
            'categories'        => $this->get( 'vs_payment.repository.pricing_plan_category' )->findAll(),
            'taxonomyId'        => $taxonomy ? $taxonomy->getId() : 0,
            'translations'      => $translations,
            'selectedTaxonIds'  => $selectedTaxonIds,
            'intlCurrencies'    => $currencies,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $categories = new ArrayCollection();
        $pcr        = $this->get( 'vs_payment.repository.pricing_plan_category' );
        $pspr       = $this->get( 'vs_users_subscriptions.repository.payed_service_subscription_period' );
        
        $formLocale = $request->request->get( 'locale' );
        $formPost   = $request->request->all( 'pricing_plan_form' );
        $formTaxon  = isset( $formPost['category_taxon'] ) ? $formPost['category_taxon'] : null;
        
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
        }
        
        if ( ! empty( $formPost['paidServicesData'] ) ) {
            foreach ( $formPost['paidServicesData'] as $paidServicePeriodId ) {
                $paidServicePeriod  = $pspr->find( $paidServicePeriodId );
                if ( $paidServicePeriod ) {
                    $entity->addPaidService( $paidServicePeriod );
                }
            }
        }
    }
    
    private function getTranslations()
    {
        $translations   = [];
        $transRepo      = $this->get( 'vs_application.repository.translation' );
        
        foreach ( $this->getRepository()->findAll() as $pricingPlan ) {
            $translations[$pricingPlan->getId()] = array_keys( $transRepo->findTranslations( $pricingPlan ) );
        }
        
        return $translations;
    }
}
