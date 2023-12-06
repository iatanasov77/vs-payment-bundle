<?php namespace Vankosoft\PaymentBundle\Component\Payum\Stripe;

use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPlans;

final class Api
{
    /** @var Gateway */
    private $gateway;
    
    public function __construct(
        Payum $payum
    ) {
        $this->gateway  = $payum->getGateway( 'stripe_js' );
    }
    
    public function getPlans()
    {
        $stripeRequest  = new \ArrayObject( [] );
        $this->gateway->execute( $getPlansRequest = new GetPlans( $stripeRequest ) );
        
        $availablePlans = $getPlansRequest->getFirstModel()->getArrayCopy();
        echo "<pre>"; var_dump( $availablePlans["data"] ); die;
    }
    
    public function createPlan()
    {
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
    }
}