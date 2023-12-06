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
        $this->stripeApi->getPlans();
        
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
            $this->stripeApi->createPlan();
            
            return $this->redirectToRoute( 'gateway_config_stripe_subscription_plans_index' );
        }
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_plans_create.html.twig', [
            'form'  => $form->createView(),
        ]);
    }
}