<?php namespace Vankosoft\PaymentBundle\Component\Payum\Stripe;

use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPlans;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetProducts;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateProduct;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPrices;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreatePrice;

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
        
        return $availablePlans["data"];
    }
    
    public function createPlan( \ArrayObject $plan )
    {
        try {
            $this->gateway->execute( new CreatePlan( $plan ) );
        } catch ( \Exception $e ) {
            die( $e->getMessage() );
        }
    }
    
    public function getProducts()
    {
        $stripeRequest      = new \ArrayObject( [] );
        $this->gateway->execute( $getProductsRequest = new GetProducts( $stripeRequest ) );
        
        $availableProducts  = $getProductsRequest->getFirstModel()->getArrayCopy();
        
        return $availableProducts["data"];
    }
    
    public function createProduct( array $formData )
    {
        $product    = new \ArrayObject([
            //"id"        => "sugarbabes_movies_month",
            //"id"    => $formData['id'],
            
            //"name"  => "SugarBabes - Watch Movies - 1 Month",
            "name"  => $formData['name'],
        ]);
        
        try {
            $this->gateway->execute( new CreateProduct( $product ) );
        } catch ( \Exception $e ) {
            die( $e->getMessage() );
        }
    }
    
    public function getPrices()
    {
        $stripeRequest      = new \ArrayObject( [] );
        $this->gateway->execute( $getPricesRequest = new GetPrices( $stripeRequest ) );
        
        $availablePrices    = $getPricesRequest->getFirstModel()->getArrayCopy();
        
        return $availablePrices["data"];
    }
    
    public function createPrice( \ArrayObject $price )
    {
        try {
            $this->gateway->execute( new CreatePrice( $price ) );
        } catch ( \Exception $e ) {
            die( $e->getMessage() );
        }
    }
}