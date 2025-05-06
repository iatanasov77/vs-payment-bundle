<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\AdjustmentInterface;

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
    protected $qty = 1;
    
    /** @var int */
    protected $total = 0;
    
    /** @var Collection<array-key, AdjustmentInterface> */
    protected $adjustments;
    
    /** @var int */
    protected $adjustmentsTotal = 0;
    
    public function __construct()
    {
        $this->adjustments = new ArrayCollection();
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
        $currentOrder = $this->getOrder();
        if ($currentOrder === $order) {
            return;
        }
        
        $this->order = null;
        
        if (null !== $currentOrder) {
            $currentOrder->removeItem($this);
        }
        
        if (null === $order) {
            return;
        }
        
        $this->order = $order;
        
        if (!$order->hasItem($this)) {
            $order->addItem($this);
        }
        
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
    
    public function getTotal(): int
    {
        return $this->total;
    }
    
    public function setTotal($total)
    {
        $this->total = $total;
        
        return $this;
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
    
    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }
        
        return $this->adjustments->filter(static function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
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
    
    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }
        
        $this->recalculateAdjustmentsTotal();
    }
    
    /**
     * Recalculates total after units total or adjustments total change.
     */
    protected function recalculateTotal(): void
    {
        $this->total = $this->adjustmentsTotal;
        
        if ($this->total < 0) {
            $this->total = 0;
        }
        
        if (null !== $this->order) {
            $this->order->recalculateItemsTotal();
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
