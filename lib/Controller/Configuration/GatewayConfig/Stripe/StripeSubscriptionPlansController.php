<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\PaymentBundle\Form\StripeSubscriptionPlanForm;
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
        $availablePlans     = $this->stripeApi->getPlans();
//         echo "<pre>"; var_dump( $availablePlans ); die;
        
        $availableProducts  = $this->stripeApi->getProducts();
//         echo "<pre>"; var_dump( $availableProducts ); die;
        
        $availablePrices    = $this->stripeApi->getPrices();
//         echo "<pre>"; var_dump( $availablePrices ); die;
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_index.html.twig', [
            'availablePlans'    => $availablePlans,
            'availableProducts' => $availableProducts,
            'availablePrices'   => $availablePrices,
        ]);
    }
    
    public function createPlanAction( Request $request ): Response
    {
        $form   = $this->createForm( StripeSubscriptionPlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            $plan       = new \ArrayObject([
                //"id"        => "sugarbabes_movies_month",
                "id"        => $formData['id'],
                
                "amount"    => $formData['amount'] * 100,
                "currency"  => \strtolower( $formData['currency'] ),
                "interval"  => "month",
                
                "product"   => [
                    //"name"  => "SugarBabes - Watch Movies - 1 Month",
                    "name"  => $formData['productName'],
                ],
            ]);
            
            $this->stripeApi->createPlan( $plan );
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_plan.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function createProductAction( Request $request ): Response
    {
        $form   = $this->createForm( StripeSubscriptionPlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            // $formData['pricingPlan']
            $this->stripeApi->createPlan();
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_plan.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
    
    public function createPriceAction( Request $request ): Response
    {
        $form   = $this->createForm( StripeSubscriptionPlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            // $formData['pricingPlan']
            $this->stripeApi->createPlan();
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_objects_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_objects_create_plan.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
}