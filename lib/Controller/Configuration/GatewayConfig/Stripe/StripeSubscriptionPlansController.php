<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\PaymentBundle\Form\Stripe\PlanForm;
use Vankosoft\PaymentBundle\Form\Stripe\ProductForm;
use Vankosoft\PaymentBundle\Form\Stripe\PriceForm;
use Vankosoft\PaymentBundle\Form\Stripe\WebhookEndpointForm;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class StripeSubscriptionPlansController extends AbstractController
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
        $availablePlans             = $this->stripeApi->getPlans();
//         echo "<pre>"; var_dump( $availablePlans ); die;
        
        $availableProducts          = $this->stripeApi->getProducts();
//         echo "<pre>"; var_dump( $availableProducts ); die;
        
        $availablePrices            = $this->stripeApi->getPrices();
//         echo "<pre>"; var_dump( $availablePrices ); die;

        $availableCustomers     = $this->stripeApi->getCustomers();
//         echo "<pre>"; var_dump( $availableCustomers ); die;

        $availableSubscriptions     = $this->stripeApi->getSubscriptions();
//         echo "<pre>"; var_dump( $availableSubscriptions ); die;
        
        $availableWebhookEndpoints  = $this->stripeApi->getWebhookEndpoints();
//         echo "<pre>"; var_dump( $availableWebhookEndpoints ); die;
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_index.html.twig', [
            'availablePlans'            => $availablePlans,
            'availableProducts'         => $availableProducts,
            'availablePrices'           => $availablePrices,
            'availableCustomers'        => $availableCustomers,
            'availableSubscriptions'    => $availableSubscriptions,
            'availableWebhookEndpoints' => $availableWebhookEndpoints,
        ]);
    }
    
    public function createPlanAction( Request $request ): Response
    {
        $form   = $this->createForm( PlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $this->stripeApi->createPlan( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_plan.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function createProductAction( Request $request ): Response
    {
        $form   = $this->createForm( ProductForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $this->stripeApi->createProduct( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_product.html.twig', [
            'form'  => $form->createView(),
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
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_price.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function cancelSubscriptionAction( $id, Request $request ): Response
    {
        $this->stripeApi->cancelSubscription( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
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
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_webhook_endpoint.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
}