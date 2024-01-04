<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentMethodInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CouponInterface;

class Order implements OrderInterface
{
    use TimestampableTrait;
    
    const STATUS_SHOPPING_CART  = 'shopping_cart';
    const STATUS_PAID_ORDER     = 'paid_order';
    const STATUS_PENDING_ORDER  = 'pending_order';  // When Order is Waiting for Payment (For Example: Used Offline BankTransfer)
    const STATUS_FAILED_ORDER   = 'failed_order';
    
    /** @var int */
    protected $id;
    
    /** @var UserPaymentAwareInterface */
    protected $user;
    
    /** @var PaymentMethodInterface */
    protected $paymentMethod;
    
    /** @var CouponInterface */
    protected $coupon;
    
    /** @var PaymentInterface */
    protected $payment;
    
    /** @var float */
    protected $totalAmount;
    
    /** @var string */
    protected $currencyCode;
    
    /** @var string */
    protected $description;
    
    /** @var Collection|OrderItemInterface[] */
    protected $items;
    
    /**
     * NEED THIS BECAUSE ORDER SHOULD BE CREATED BEFORE THE PAYMENT IS PRAPARED AND DONE
     * https://dev.to/qferrer/purging-expired-carts-building-a-shopping-cart-with-symfony-3eff
     * 
     * @var enum
     */
    protected $status;
    
    /** @var string */
    protected $sessionId;
    
    /** @var bool */
    protected $recurringPayment = false;
    
    public function __construct()
    {
        $this->items        = new ArrayCollection();
        
        /** 
         * Set Default Values
         */
        $this->totalAmount  = 0;
        $this->currencyCode = 'EUR';
        $this->description  = 'VankoSoft Payment';
        $this->status       = self::STATUS_SHOPPING_CART;
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
    
    public function getCoupon()
    {
        return $this->coupon;
    }
    
    public function setCoupon($coupon)
    {
        $this->coupon   = $coupon;
        
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
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        
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
            
            $this->totalAmount += $item->getPrice();
        }
    }
    
    public function removeItem( OrderItemInterface $item )
    {
        if( $this->items->contains( $item ) ) {
            $this->items->removeElement( $item );
            $item->setOrder( null );
            
            $this->totalAmount -= $item->getPrice();
        }
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status    = $status;
        
        return $this;
    }
    
    public function getSessionId()
    {
        return $this->sessionId;
    }
    
    public function setSessionId($sessionId)
    {
        $this->sessionId    = $sessionId;
        
        return $this;
    }
    
    public function hasRecurringPayment(): bool
    {
        return $this->recurringPayment;
    }
    
    public function setRecurringPayment($recurringPayment)
    {
        $this->recurringPayment    = $recurringPayment;
        
        return $this;
    }
    
    public function isPaid()
    {
        return $this->status === self::STATUS_PAID_ORDER;
    }
    
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING_ORDER;
    }
    
    public function getSubscriptions(): array
    {
        $subscriptions  = [];
        foreach ( $this->items as $item ) {
            $subscription   = $item->getSubscription();
            if ( $subscription ) {
                $subscriptions[]    = $subscription;
            }
        }
        
        return $subscriptions;
    }
}
