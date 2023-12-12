<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface;
use Vankosoft\PaymentBundle\Component\PayableObject;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductInterface;

class OrderItem implements OrderItemInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var Interfaces\OrderInterface
     */
    protected $order;
    
    /**
     * @var Interfaces\PricingPlanSubscriptionInterface
     */
    protected $subscription;
    
    /**
     * @var Interfaces\ProductInterface
     */
    protected $product;
    
    /**
     * The Class of the payable object
     * 
     * @var string
     */
    protected $payableObjectType;
    
    /**
     * @var float
     */
    protected $price;
    
    /**
     * @var string
     */
    protected $currencyCode;
    
    /**
     * @var int
     */
    protected $qty;
    
    public function __construct()
    {
        $this->qty  = 1;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function setOrder($order)
    {
        $this->order = $order;
        
        return $this;
    }
    
    public function getSubscription()
    {
        return $this->subscription;
    }
    
    public function setSubscription($subscription)
    {
        $this->subscription         = $subscription;
        $this->payableObjectType    = get_class( $subscription );
        
        return $this;
    }
    
    public function getProduct()
    {
        return $this->product;
    }
    
    public function setProduct($product)
    {
        $this->product              = $product;
        $this->payableObjectType    = get_class( $product );
        
        return $this;
    }
    
    public function getPayableObjectType()
    {
        return $this->payableObjectType;
    }
    
    public function setPayableObjectType($payableObjectType)
    {
        $this->payableObjectType = $payableObjectType;
        
        return $this;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }
    
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
    
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
        
        return $this;
    }
    
    public function getQty()
    {
        return $this->qty;
    }
    
    public function setQty($qty)
    {
        $this->qty = $qty;
        
        return $this;
    }
    
    public function getObject(): PayableObjectInterface
    {
        switch ( $this->getPayableObjectType() ) {
            case 'App\Entity\Payment\PricingPlan':
                return $this->getSubscription();
                break;
            case 'App\Entity\Payment\Product':
                return $this->getProduct();
                break;
            default:
                throw new \Exception( 'Wrong Order Item !!!' );
        }
    }
}
