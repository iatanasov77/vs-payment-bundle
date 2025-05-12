<?php namespace Vankosoft\PaymentBundle\Controller\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Form\Stripe\PlanForm;
use Vankosoft\PaymentBundle\Form\Stripe\ProductForm;
use Vankosoft\PaymentBundle\Form\Stripe\PriceForm;

class PricingPlansController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var Environment */
    private $templatingEngine;
    
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        ManagerRegistry $doctrine,
        Environment $templatingEngine,
        StripeApi $stripeApi
    ) {
        $this->doctrine         = $doctrine;
        $this->templatingEngine = $templatingEngine;
        $this->stripeApi        = $stripeApi;
    }
    
    public function createPlanAction( Request $request ): Response
    {
        $form   = $this->createForm( PlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $this->stripeApi->createPlan( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_plan.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function createProductAction( Request $request ): Response
    {
        $form   = $this->createForm( ProductForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $this->stripeApi->createProduct( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_product.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function createPriceAction( Request $request ): Response
    {
        $form   = $this->createForm( PriceForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            
            $priceData      = $this->stripeApi->createPrice( $formData );
            $ppAttributes   = $formData['pricingPlan']->getGatewayAttributes();
            $ppAttributes   = $ppAttributes ?: [];
            
            $ppAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY]    = $priceData['id'];
            $formData['pricingPlan']->setGatewayAttributes( $ppAttributes );
            
            $this->doctrine->getManager()->persist( $formData['pricingPlan'] );
            $this->doctrine->getManager()->flush();
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_price.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
}