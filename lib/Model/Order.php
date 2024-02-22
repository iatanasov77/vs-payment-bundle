<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;

use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentMethodInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;

//use Vankosoft\PaymentBundle\Model\Interfaces\CouponInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionCouponInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\AdjustmentInterface;

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
    
    /** @var UserPaymentAwareInterface */
    protected $application;
    
    /** @var PaymentMethodInterface */
    protected $paymentMethod;
    
    /** @var PaymentInterface */
    protected $payment;
    
    /**
     * @deprecated since Version 3.1, use $this->total instead.
     * @var float
     */
    protected $totalAmount;
    
    /** @var string */
    protected $currencyCode;
    
    /** @var string */
    protected $description;
    
    /** @var Collection|OrderItemInterface[] */
    protected $items;
    
    /** @var int */
    protected $itemsTotal = 0;
    
    /** @var PromotionCouponInterface */
    protected $promotionCoupon;
    
    /** @var Collection<array-key, PromotionInterface> */
    protected $promotions;
    
    /** @var Collection<array-key, AdjustmentInterface> */
    protected $adjustments;
    
    /** @var int */
    protected $adjustmentsTotal = 0;
    
    /**
     * Items total + adjustments total.
     *
     * @var int
     */
    protected $total = 0;
    
    /**
     * NEED THIS BECAUSE ORDER SHOULD BE CREATED BEFORE THE PAYMENT IS PRAPARED AND DONE
     * https://dev.to/qferrer/purging-expired-carts-building-a-shopping-cart-with-symfony-3eff
     * 
     * @var string
     */
    protected $status;
    
    /** @var string */
    protected $sessionId;
    
    /** @var bool */
    protected $recurringPayment = false;
    
    public function __construct()
    {
        $this->items        = new ArrayCollection();
        
        $this->adjustments  = new ArrayCollection();
        
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
    
    public function getApplication(): ?ApplicationInterface
    {
        return $this->application;
    }
    
    public function setApplication(?ApplicationInterface $application): void
    {
        $this->application = $application;
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
            $this->itemsTotal += $item->getTotal();
            
            $this->items->add( $item );
            $item->setOrder( $this );
            
            /** @deprecated since Version 3.1, use $this->total instead. */
            $this->totalAmount += $item->getPrice();
            
            $this->recalculateTotal();
        }
    }
    
    public function removeItem( OrderItemInterface $item )
    {
        if( $this->items->contains( $item ) ) {
            $this->items->removeElement( $item );
            $item->setOrder( null );
            
            /** @deprecated since Version 3.1, use $this->total instead. */
            $this->totalAmount -= $item->getPrice();
            
            $this->itemsTotal -= $item->getTotal();
            $this->recalculateTotal();
        }
    }
    
    public function hasItem( OrderItemInterface $item ): bool
    {
        return $this->items->contains($item);
    }
    
    public function getItemsTotal(): int
    {
        return $this->itemsTotal;
    }
    
    public function recalculateItemsTotal(): void
    {
        $this->itemsTotal = 0;
        foreach ($this->items as $item) {
            $this->itemsTotal += $item->getTotal();
        }
        
        $this->recalculateTotal();
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
    
    public function getTotalQuantity(): int
    {
        $quantity = 0;
        
        foreach ( $this->items as $item ) {
            $quantity += $item->getQuantity();
        }
        
        return $quantity;
    }
    
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }
    
    public function getPromotionCoupon(): ?PromotionCouponInterface
    {
        return $this->promotionCoupon;
    }
    
    public function setPromotionCoupon(?PromotionCouponInterface $coupon): void
    {
        $this->promotionCoupon = $coupon;
    }
    
    public function getPromotionSubjectTotal(): int
    {
        return $this->getItemsTotal();
    }
    
    public function getPromotionSubjectCount(): int
    {
        return $this->getTotalQuantity();
    }
    
    public function getPromotions(): Collection
    {
        return $this->promotions;
    }
    
    public function addPromotion(BasePromotionInterface $promotion): void
    {
        if (!$this->hasPromotion($promotion)) {
            $this->promotions->add($promotion);
        }
    }
    
    public function removePromotion(BasePromotionInterface $promotion): void
    {
        if ($this->hasPromotion($promotion)) {
            $this->promotions->removeElement($promotion);
        }
    }
    
    public function hasPromotion(BasePromotionInterface $promotion): bool
    {
        return $this->promotions->contains($promotion);
    }
    
    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }
        
        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }
    
    public function getAdjustmentsRecursively(?string $type = null): Collection
    {
        $adjustments = clone $this->getAdjustments($type);
        foreach ($this->items as $item) {
            foreach ($item->getAdjustmentsRecursively($type) as $adjustment) {
                $adjustments->add($adjustment);
            }
        }
        
        return $adjustments;
    }
    
    public function addAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$this->hasAdjustment($adjustment)) {
            $this->adjustments->add($adjustment);
            $this->addToAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable($this);
            $this->recalculateAdjustmentsTotal();
        }
    }
    
    public function removeAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isLocked() && $this->hasAdjustment($adjustment)) {
            $this->adjustments->removeElement($adjustment);
            $this->subtractFromAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable(null);
            $this->recalculateAdjustmentsTotal();
        }
    }
    
    public function hasAdjustment(AdjustmentInterface $adjustment): bool
    {
        return $this->adjustments->contains($adjustment);
    }
    
    public function getAdjustmentsTotal(?string $type = null): int
    {
        if (null === $type) {
            return $this->adjustmentsTotal;
        }
        
        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }
        
        return $total;
    }
    
    public function getAdjustmentsTotalRecursively(?string $type = null): int
    {
        $total = 0;
        foreach ($this->getAdjustmentsRecursively($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }
        
        return $total;
    }
    
    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            if ($adjustment->isLocked()) {
                continue;
            }
            
            $this->removeAdjustment($adjustment);
        }
        
        $this->recalculateAdjustmentsTotal();
    }
    
    public function removeAdjustmentsRecursively(?string $type = null): void
    {
        $this->removeAdjustments($type);
        foreach ($this->items as $item) {
            $item->removeAdjustmentsRecursively($type);
        }
    }
    
    public function recalculateAdjustmentsTotal(): void
    {
        $this->adjustmentsTotal = 0;
        
        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }
        
        $this->recalculateTotal();
    }
    
    public function canBeProcessed(): bool
    {
        return $this->status === self::STATUS_SHOPPING_CART;
    }
    
    /**
     * Items total + Adjustments total.
     */
    protected function recalculateTotal(): void
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;
        
        if ($this->total < 0) {
            $this->total = 0;
        }
    }
    
    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }
    
    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }
}
