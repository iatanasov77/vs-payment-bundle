<?php namespace Vankosoft\PaymentBundle\Controller\PromotionCoupons;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;

class PromotionsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $translations   = $this->classInfo['action'] == 'indexAction' ? $this->getTranslations() : [];
        
        return [
            'translations'  => $translations,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        $formPost   = $request->request->all( 'vs_payment_promotion' );
        
        $rules      = new ArrayCollection();
        foreach ( $formPost['rules'] as $rule ) {
            if ( empty( $rule['type'] ) ) {
                continue;
            }
            
            
        }
        
        $actions    = new ArrayCollection();
        foreach ( $formPost['actions'] as $action ) {
            if ( empty( $action['type'] ) ) {
                continue;
            }
            
            
        }
        
        $entity->setRules( $rules );
        $entity->setActions( $actions );
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