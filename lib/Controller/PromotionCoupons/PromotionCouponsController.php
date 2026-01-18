<?php namespace Vankosoft\PaymentBundle\Controller\PromotionCoupons;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Vankosoft\PaymentBundle\Form\PromotionCouponGeneratorForm;

class PromotionCouponsController extends AbstractCrudController
{
    /**
     * @throws NotFoundHttpException
     */
    public function generateAction( Request $request ): Response
    {
        $configuration = $this->requestConfigurationFactory->create( $this->metadata, $request );
        
        if ( null === $promotionId = $request->attributes->get( 'promotionId' ) ) {
            throw new NotFoundHttpException( 'No promotion id given.' );
        }
        
        if ( null === $promotion = $this->container->get( 'vs_payment.repository.promotion' )->find( $promotionId ) ) {
            throw new NotFoundHttpException( 'Promotion not found.' );
        }
        
        $form = $this->container->get( 'form.factory' )->create( PromotionCouponGeneratorForm::class );
        $form->handleRequest( $request );
        
        if ( $form->isSubmitted() && $form->isValid() ) {
            $this->container->get( 'vs_payment.promotion_coupon_generator' )->generate( $promotion, $form->all() );
            $this->flashHelper->addSuccessFlash( $configuration, 'generate' );
            
            //return $this->redirectHandler->redirectToResource( $configuration, $promotion );
            return $this->redirect( $this->generateUrl( 'vs_payment_promotion_coupon_index', ['promotionId' => $promotionId] ) );
        }
        
        return $this->render(
            $configuration->getTemplate( 'generate.html' ),
            [
                'configuration' => $configuration,
                'metadata'      => $this->metadata,
                'promotion'     => $promotion,
                'form'          => $form->createView(),
            ]
        );
    }
    
    protected function customData( Request $request, $entity = null ): array
    {
        $promotion  = $this->get( 'vs_payment.repository.promotion' )->find( $request->get( 'promotionId' ) );
        
        return [
            'promotion' => $promotion,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request ): void
    {
        $promotion  = $this->get( 'vs_payment.repository.promotion' )->find( $request->get( 'promotionId' ) );
        $entity->setPromotion( $promotion );
    }
}