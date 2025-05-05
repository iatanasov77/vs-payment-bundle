<?php namespace Vankosoft\PaymentBundle\Component\Payum\Stripe;

use Payum\Core\Payum;
use Payum\Core\Gateway;
use Payum\Stripe\Request\Api\CreatePlan;
use Payum\Stripe\Request\Api\CreateCustomer;
use Stripe\Event as StripeEvent;
use Http\Discovery\NotFoundException;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPlans;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetProducts;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateProduct;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPrices;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreatePrice;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetCustomers;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetPaymentMethods;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetSubscriptions;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CancelSubscription;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetConnectedAccounts;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetWebhookEndpoints;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateWebhookEndpoint;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\GetCoupons;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CreateCoupon;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\RetrieveCoupon;

final class Api
{
    const PRODUCT_ATTRIBUTE_KEY         = 'stripe_product_id';
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
        
        return isset( $availablePlans["data"] ) ? $availablePlans["data"] : [];
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
        
        return isset( $availableProducts["data"] ) ? $availableProducts["data"] : [];
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
        
        return isset( $availablePrices["data"] ) ? $availablePrices["data"] : [];
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
    
    public function getCustomers( array $params = [] )
    {
        $stripeRequest      = new \ArrayObject( $params );
        $this->gateway->execute( $getCustomersRequest = new GetCustomers( $stripeRequest ) );
        
        $availableCustomers = $getCustomersRequest->getFirstModel()->getArrayCopy();
        
        return isset( $availableCustomers["data"] ) ? $availableCustomers["data"] : [];
    }
    
    public function createCustomer( array $formData )
    {
        $custommer  = new \ArrayObject([
            'name'  => $formData['name'],
            'email' => $formData['email'],
        ]);
        $this->gateway->execute( $createCustommerRequest = new CreateCustomer( $custommer ) );
        
        return $createCustommerRequest->getFirstModel()->getArrayCopy();
    }
    
    public function getPaymentMethods( array $params = [] )
    {
        $stripeRequest              = new \ArrayObject( $params );
        $this->gateway->execute( $getPaymentMethodsRequest = new GetPaymentMethods( $stripeRequest ) );
        
        $availablePaymentMethods    = $getPaymentMethodsRequest->getFirstModel()->getArrayCopy();
        
        return isset( $availablePaymentMethods["data"] ) ? $availablePaymentMethods["data"] : [];
    }
    
    public function getSubscriptions( array $params = [] )
    {
        $stripeRequest      = new \ArrayObject( $params );
        $this->gateway->execute( $getSubscriptionsRequest = new GetSubscriptions( $stripeRequest ) );
        
        $availableSubscriptions = $getSubscriptionsRequest->getFirstModel()->getArrayCopy();
        
        return isset( $availableSubscriptions["data"] ) ? $availableSubscriptions["data"] : [];
    }
    
    public function cancelSubscription( $id )
    {
        $subscription   = new \ArrayObject([
            "id"    => $id,
        ]);
        $this->gateway->execute( new CancelSubscription( $subscription ) );
    }
    
    public function getConnectedAccounts()
    {
        $stripeRequest  = new \ArrayObject( [] );
        $this->gateway->execute( $getAccountsRequest = new GetConnectedAccounts( $stripeRequest ) );
        
        $availableAccounts = $getAccountsRequest->getFirstModel()->getArrayCopy();
        
        return isset( $availableAccounts["data"] ) ? $availableAccounts["data"] : [];
    }
    
    public function getWebhookEndpoints()
    {
        $stripeRequest      = new \ArrayObject( [] );
        $this->gateway->execute( $getWebhookEndpointsRequest = new GetWebhookEndpoints( $stripeRequest ) );
        
        $availableWebhookEndpoints  = $getWebhookEndpointsRequest->getFirstModel()->getArrayCopy();
        
        return isset( $availableWebhookEndpoints["data"] ) ? $availableWebhookEndpoints["data"] : [];
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
        
        return isset( $availableCoupons["data"] ) ? $availableCoupons["data"] : [];
    }
    
    public function createCoupon( array $formData )
    {
        $couponData = [
            'duration'  => $formData['duration'],
        ];
        
        if ( $formData['duration_in_months'] ) {
            $couponData['duration_in_months']    = $formData['duration_in_months'];
        }
        
        if ( $formData['percent_off'] ) {
            $couponData['percent_off']    = $formData['percent_off'];
        }
        
        $coupon = new \ArrayObject( $couponData );
        $this->gateway->execute( $createCouponRequest = new CreateCoupon( $coupon ) );
        
        return $createCouponRequest->getFirstModel()->getArrayCopy();
    }
    
    public function retrieveCoupon( string $id )
    {
        $coupon = new \ArrayObject( ['id' => $id] );
        $this->gateway->execute( $retrieveCouponRequest = new RetrieveCoupon( $coupon ) );
        
        return $retrieveCouponRequest->getFirstModel()->getArrayCopy();
    }
    
    public function deleteCoupon( $id )
    {
        $coupon   = new \ArrayObject([
            "id"    => $id,
        ]);
        $this->gateway->execute( new DeleteCoupon( $coupon ) );
    }
}