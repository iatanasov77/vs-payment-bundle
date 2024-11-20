<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Form\Stripe\PlanForm;
use Vankosoft\PaymentBundle\Form\Stripe\ProductForm;
use Vankosoft\PaymentBundle\Form\Stripe\PriceForm;
use Vankosoft\PaymentBundle\Form\Stripe\WebhookEndpointForm;
use Vankosoft\PaymentBundle\Form\Stripe\CouponForm;
use Vankosoft\PaymentBundle\Form\Stripe\CustomerForm;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class StripeObjectsController extends AbstractController
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
    
    public function indexAction( Request $request ): Response
    {
        $availablePlans             = $this->stripeApi->getPlans();
        $availableProducts          = $this->stripeApi->getProducts();
        $availablePrices            = $this->stripeApi->getPrices();
        $availableCustomers         = $this->stripeApi->getCustomers();
        $availablePaymentMethods    = $this->stripeApi->getPaymentMethods();
        $availableSubscriptions     = $this->stripeApi->getSubscriptions();
        $availableWebhookEndpoints  = $this->stripeApi->getWebhookEndpoints();
//         echo "<pre>"; var_dump( $availableWebhookEndpoints ); die;

        $availableCoupons           = $this->stripeApi->getCoupons();
        
        //return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/index.html.twig', [
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/index.html.twig', [
            'availablePlans'            => $availablePlans,
            'availableProducts'         => $availableProducts,
            'availablePrices'           => $availablePrices,
            'availableCustomers'        => $availableCustomers,
            'availablePaymentMethods'   => $availablePaymentMethods,
            'availableSubscriptions'    => $availableSubscriptions,
            'availableWebhookEndpoints' => $availableWebhookEndpoints,
            'availableCoupons'          => $availableCoupons,
        ]);
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
    
    public function cancelSubscriptionAction( $id, Request $request ): Response
    {
        $this->stripeApi->cancelSubscription( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
    }
    
    public function createCustomerAction( Request $request ): Response
    {
        $form   = $this->createForm( CustomerForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            $this->stripeApi->createCustomer( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_customer.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function showCustomerPaymentMethods( $customerId, Request $request ): Response
    {
        $availablePaymentMethods    = $this->stripeApi->getPaymentMethods([
            'customer' => $customerId,
        ]);
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/stripe-payment-methods-table.html.twig', [
            'availablePaymentMethods'   => $availablePaymentMethods,
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function createWebhookEndpointAction( Request $request ): Response
    {
        $form   = $this->createForm( WebhookEndpointForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            if ( empty( $formData['enabled_events'] ) ) {
                throw new \RuntimeException( 'Enabled Events field cannot be empty !!!' );
            }
            
            $this->stripeApi->createWebhookEndpoint( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_webhook_endpoint.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
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