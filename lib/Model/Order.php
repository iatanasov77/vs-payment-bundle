<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Interfaces\OrderItemInterface;

class Order implements Interfaces\OrderInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var \Vankosoft\PaymentBundle\Model\Interfaces\PaymentsUserInterface
     */
    protected $user;
    
    /**
     * @var Interfaces\PaymentMethodInterface
     */
    protected $paymentMethod;
    
    /**
     * @var Interfaces\PaymentInterface
     */
    protected $payment;
    
    /**
     * @var float
     */
    protected $totalAmount;
    
    /**
     * @var string
     */
    protected $currencyCode;
    
    /**
     * @var Collection|OrderItemInterface[]
     */
    protected $items;
    
    public function __construct()
    {
        $this->items    = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
    
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        
        return $this;
    }
    
    public function getPayment()
    {
        return $this->payment;
    }
    
    public function setPayment($payment)
    {
        $this->payment = $payment;
        
        return $this;
    }
    
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }
    
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;
        
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
    
    public function getItems()
    {
        return $this->items;
    }
    
    public function setItems($items)
    {
        $this->items    = $items;
        
        return $this;
    }
    
    public function addItem( OrderItemInterface $item )
    {
        if( ! $this->items->contains( $item ) ) {
            $this->items->add( $item );
            $item->setOrder( $this );
        }
    }
    
    public function removeItem( OrderItemInterface $item )
    {
        if( $this->items->contains( $item ) ) {
            $this->items->removeElement( $item );
            $item->setOrder( null );
        }
    }
}
