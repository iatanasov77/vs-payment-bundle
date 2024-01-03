<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Form\Stripe\CouponForm;

class StripeCouponsController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        ManagerRegistry $doctrine,
        StripeApi $stripeApi
    ) {
        $this->doctrine     = $doctrine;
        $this->stripeApi    = $stripeApi;
    }
    
    public function indexAction( Request $request ): Response
    {
        $availableCoupons   = $this->stripeApi->getCoupons();
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/coupon_objects_index.html.twig', [
            'availableCoupons'  => $availableCoupons,
        ]);
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
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/coupon_objects_create.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function retrieveCouponAction( $id, Request $request ): Response
    {
        $couponData = $this->stripeApi->retrieveCoupon( $id );
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/coupon_objects_retrieve.html.twig', [
            'coupon'    => $couponData,
        ]);
    }
    
    public function deleteCouponAction( $id, Request $request ): Response
    {
        $this->stripeApi->deleteCoupon( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_coupon_objects_index' );
    }
}