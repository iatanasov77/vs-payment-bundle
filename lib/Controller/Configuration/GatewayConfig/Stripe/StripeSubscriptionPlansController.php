<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;
use Vankosoft\PaymentBundle\Form\StripeSubscriptionPlanForm;

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
                "amount"    => 10,
                "interval"  => "month",
                "name"      => "SugarBabes - Watch Movies - 1 Month",
                "currency"  => "eur",
                "id"        => "sugarbabes_movies_month"
            ]);
            
            $this->gateway->execute( new CreatePlan( $plan ) );
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_plans_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_plans_create.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
}