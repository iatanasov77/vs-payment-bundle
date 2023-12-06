<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;
use Vankosoft\PaymentBundle\Form\StripeSubscriptionPlanForm;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPlans;

class StripeSubscriptionPlansController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var Gateway */
    private $gateway;
    
    public function __construct(
        ManagerRegistry $doctrine,
        Payum $payum
    ) {
        $this->doctrine = $doctrine;
        $this->gateway  = $payum->getGateway( 'stripe_js' );
    }
    
    public function indexAction( Request $request ): Response
    {
        $stripeRequest  = new \ArrayObject( [] );
        $this->gateway->execute( $getPlansRequest = new GetPlans( $stripeRequest ) );
        
        $availablePlans = $getPlansRequest->getFirstModel();
        echo "<pre>"; var_dump( $availablePlans ); die;
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_plans_index.html.twig', [
            'items' => [],
        ]);
    }
    
    public function createAction( Request $request ): Response
    {
        $form   = $this->createForm( StripeSubscriptionPlanForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData   = $form->getData();
            
            // $formData['pricingPlan']
            $plan       = new \ArrayObject([
                "id"        => "sugarbabes_movies_month",
                
                "amount"    => 10,
                "currency"  => "eur",
                "interval"  => "month",
                
                "product"   => [
                    "name"  => "SugarBabes - Watch Movies - 1 Month",
                ],
            ]);
            
            try {
                $this->gateway->execute( new CreatePlan( $plan ) );
            } catch ( \Exception $e ) {
                die( $e->getMessage() );
            }
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_plans_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_plans_create.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
}