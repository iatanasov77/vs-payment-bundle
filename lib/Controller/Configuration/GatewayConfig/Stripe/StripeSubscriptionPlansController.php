<?php namespace Vankosoft\PaymentBundle\Controller\Configuration\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Payum\Core\Payum;
use Payum\Stripe\Request\Api\CreatePlan;

class StripeSubscriptionPlansController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
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
        var_dump($this->gateway); die;
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/subscription_plans_index.html.twig' );
    }
    
    public function createAction( Request $request ): Response
    {
        $plan           = new \ArrayObject([
            "amount"    => $order->getTotalAmount(),
            "interval"  => "month",
            "name"      => $pricingPlan->getTitle(),
            "currency"  => $order->getCurrencyCode(),
            
            // Pricing Plan Monthly ( Created From Stripe Dashbord )
            "id"        => "price_1O05sBCozROjz2jXEwka0bux"
        ]);
        
        $this->gateway->execute( new CreatePlan( $plan ) );
        
        
    }
}