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
