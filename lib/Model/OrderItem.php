<?php namespace Vankosoft\PaymentBundle\Model;

class OrderItem implements Interfaces\OrderItemInterface
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
     * @var Interfaces\PayableObjectInterface
     */
    protected $object;
    
    /**
     * @var Interfaces\ProductInterface
     */
    protected $product;
    
    /**
     * The Class of the object
     * 
     * @var string
     */
    protected $objectType;
    
    /**
     * @var float
     */
    protected $price;
    
    /**
     * @var string
     */
    protected $currencyCode;
    
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
    
    public function getObject()
    {
        return $this->object;
    }
    
    public function setObject($object)
    {
        $this->object = $object;
        $this->objectType   = get_class( $object );
        
        return $this;
    }
    
    public function getProduct()
    {
        return $this->product;
    }
    
    public function setProduct($product)
    {
        $this->product = $product;
        $this->objectType   = get_class( $product );
        
        return $this;
    }
    
    public function getObjectType()
    {
        return $this->objectType;
    }
    
    public function setObjectType($objectType)
    {
        $this->objectType = $objectType;
        
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
}
