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
    const PRICING_PLAN_ATTRIBUTE_KEY    = 'stripe_plan_id';
    
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
    
    public function createPlan( array $formData )
    {
        $plan       = new \ArrayObject([
            //"id"        => "sugarbabes_movies_month",
            "id"        => $formData['id'],
            
            "amount"    => $formData['amount'] * 100,
            "currency"  => \strtolower( $formData['currency'] ),
            "interval"  => $formData['interval'],
            
            "product"   => [
                //"name"  => "SugarBabes - Watch Movies - 1 Month",
                "name"  => $formData['productName'],
            ],
        ]);
        $this->gateway->execute( new CreatePlan( $plan ) );
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
        $this->gateway->execute( new CreateProduct( $product ) );
    }
    
    public function getPrices()
    {
        $stripeRequest      = new \ArrayObject( [] );
        $this->gateway->execute( $getPricesRequest = new GetPrices( $stripeRequest ) );
        
        $availablePrices    = $getPricesRequest->getFirstModel()->getArrayCopy();
        
        return $availablePrices["data"];
    }
    
    public function createPrice( array $formData )
    {
        $price      = new \ArrayObject([
            //"id"            => $formData['id'],
            'product'       => $formData['product'],
            
            'unit_amount'   => $formData['amount'] * 100,
            'currency'      => \strtolower( $formData['currency'] ),
            
            'recurring'     => [
                'interval'          => $formData['interval'],
                'interval_count'    => $formData['intervalCount'],
            ],
        ]);
        $this->gateway->execute( $createPriceRequest = new CreatePrice( $price ) );
        
        return $createPriceRequest->getFirstModel()->getArrayCopy();
    }
    
    public function getProductPairs()
    {
        $products       = $this->getProducts();
        
        $productPairs   = [];
        foreach ( $products as $product ) {
            $productPairs[$product['id']]   = $product['name'];
        }
        
        return $productPairs;
    }
}