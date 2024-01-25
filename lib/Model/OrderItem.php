<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;

class OrderItem implements OrderItemInterface
{
    /** @var int */
    protected $id;
    
    /** @var Interfaces\OrderInterface */
    protected $order;
    
    /**
     * The Class of the payable object
     *
     * @var string
     */
    protected $payableObjectType;
    
    /** @var float */
    protected $price;
    
    /** @var string */
    protected $currencyCode;
    
    /** @var int */
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
    
    public function getPayableObjectType(): string
    {
        return $this->payableObjectType;
    }
    
    public function setPayableObjectType( string $payableObjectType ): self
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
}
