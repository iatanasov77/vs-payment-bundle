<?php namespace Vankosoft\PaymentBundle\Controller\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Form\Stripe\CouponForm;

class CouponsController extends AbstractController
{
    /** @var Environment */
    private $templatingEngine;
    
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        Environment $templatingEngine,
        StripeApi $stripeApi
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->stripeApi        = $stripeApi;
    }
    
    public function createCouponAction( Request $request ): Response
    {
        $form   = $this->createForm( CouponForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            
            $this->stripeApi->createCoupon( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_coupon_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_coupon.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function retrieveCouponAction( $id, Request $request ): Response
    {
        $couponData = $this->stripeApi->retrieveCoupon( $id );
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/retrieve_coupon.html.twig', [
            'coupon'    => $couponData,
        ]);
    }
    
    public function deleteCouponAction( $id, Request $request ): Response
    {
        $this->stripeApi->deleteCoupon( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_coupon_objects_index' );
    }
}