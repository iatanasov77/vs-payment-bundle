<?php namespace Vankosoft\PaymentBundle\Component\Payum\Stripe;

use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;
use Stripe\Event as StripeEvent;
use Http\Discovery\NotFoundException;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPlans;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetProducts;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateProduct;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPrices;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreatePrice;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetSubscriptions;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CancelSubscription;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetWebhookEndpoints;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateWebhookEndpoint;

final class Api
{
    const PRICING_PLAN_ATTRIBUTE_KEY    = 'stripe_plan_id';
    const SUBSCRIPTION_ATTRIBUTE_KEY    = 'stripe_subscription_id';
    const CUSTOMER_ATTRIBUTE_KEY        = 'stripe_customer_id';
    const PRICE_ATTRIBUTE_KEY           = 'stripe_price_id';
    
    const STRIPE_EVENTS                 = [
        'charge.succeeded',
        'charge.failed',
        'invoice.finalized',
        'invoice.finalization_failed',
        'invoice.paid',
        'invoice.payment_failed',
        'invoice.payment_succeeded',
        'subscription_schedule.canceled',
        'subscription_schedule.created',
        'subscription_schedule.completed',
    ];
    
    /** @var Gateway */
    private $gateway;
    
    public function __construct(
        Payum $payum
    ) {
        try {
            $this->gateway  = $payum->getGateway( 'stripe_js' );
        } catch ( NotFoundException $e ) {
            
        }
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
    
    public function getSubscriptions( array $params = [] )
    {
        $stripeRequest      = new \ArrayObject( $params );
        $this->gateway->execute( $getSubscriptionsRequest = new GetSubscriptions( $stripeRequest ) );
        
        $availableSubscriptions = $getSubscriptionsRequest->getFirstModel()->getArrayCopy();
        
        return $availableSubscriptions["data"];
    }
    
    public function cancelSubscription( $id )
    {
        $subscription   = new \ArrayObject([
            "id"    => $id,
        ]);
        $this->gateway->execute( new CancelSubscription( $subscription ) );
    }
    
    public function getWebhookEndpoints()
    {
        $stripeRequest      = new \ArrayObject( [] );
        $this->gateway->execute( $getWebhookEndpointsRequest = new GetWebhookEndpoints( $stripeRequest ) );
        
        $availableWebhookEndpoints  = $getWebhookEndpointsRequest->getFirstModel()->getArrayCopy();
        
        return $availableWebhookEndpoints["data"];
    }
    
    public function createWebhookEndpoint( array $formData )
    {
        $webhookEndpoint    = new \ArrayObject([
            'enabled_events'    => $formData['enabled_events'],
            'url'               => $formData['url'],
        ]);
        $this->gateway->execute( new CreateWebhookEndpoint( $webhookEndpoint ) );
    }
    
    public function getEvent( $eventId )
    {
        return StripeEvent::retrieve( $eventId );
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
    
    public function getCoupons( array $params = [] )
    {
        $stripeRequest      = new \ArrayObject( $params );
        $this->gateway->execute( $getCouponsRequest = new GetCoupons( $stripeRequest ) );
        
        $availableCoupons = $getCouponsRequest->getFirstModel()->getArrayCopy();
        
        return $availableCoupons["data"];
    }
    
    public function createCoupon( array $formData )
    {
        $coupon = new \ArrayObject([
            'product'       => $formData['product'],
            
            'unit_amount'   => $formData['amount'] * 100,
            'currency'      => \strtolower( $formData['currency'] ),
        ]);
        $this->gateway->execute( $createCouponRequest = new CreateCoupon( $coupon ) );
        
        return $createCouponRequest->getFirstModel()->getArrayCopy();
    }
    
    public function retrieveCoupon( string $id )
    {
        $coupon = new \ArrayObject( ['id' => $id] );
        $this->gateway->execute( $retrieveCouponRequest = new RetrieveCoupon( $coupon ) );
        
        return $retrieveCouponRequest->getFirstModel()->getArrayCopy();
    }
}