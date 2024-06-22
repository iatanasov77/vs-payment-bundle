<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;

trait PayableObjectTrait
{
    /** @var integer */
    protected $price;
    
    /** @var CurrencyInterface */
    protected $currency;
    
    /**
     * @var Collection|OrderItemInterface[]
     */
    protected $orderItems;
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function setPrice( $price ): self
    {
        $this->price = $price;
        
        return $this;
    }
    
    public function getCurrency(): ?CurrencyInterface
    {
        return $this->currency;
    }
    
    public function setCurrency( CurrencyInterface $currency ): self
    {
        $this->currency = $currency;
        
        return $this;
    }
    
    public function getCurrencyCode(): ?string
    {
        if ( $this->currency ) {
            return $this->currency->getCode();
        }
        
        return null;
    }
    
    /**
     * @return Collection|OrderItemInterface[]
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }
    
    public function addOrderItem( OrderItemInterface $orderItem ): self
    {
        if ( ! $this->orderItems->contains( $orderItem ) ) {
            $this->orderItems[] = $orderItem;
        }
        
        return $this;
    }
    
    public function removeOrderItem( OrderItemInterface $orderItem ): self
    {
        if ( $this->orderItems->contains( $orderItem ) ) {
            $this->orderItems->removeElement( $orderItem );
        }
        
        return $this;
    }
}