<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CouponInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

class Coupon implements CouponInterface
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait;
    
    /** @var int */
    protected $id;
    
    /** @var string */
    protected $locale;
    
    /** @var string */
    protected $code;
    
    /** @var string|null */
    protected $name;
    
    /** @var float|null */
    protected $amountOff;
    
    /** @var CurrencyInterface|null */
    protected $currency;
    
    /** @var float|null */
    protected $percentOff;
    
    /**
     * ENUM Type
     * ---------
     * 'discount_coupon' are used to apply discount on Shopping Cart Total Amount. 
     * 'payment_coupon' requires a Pricing Plan relation and used to pay these Pricing Plans.
     * 
     * @var string
     */
    protected $type = 'discount_coupon';
    
    /** @var Collection|OrderInterface[] */
    protected $orders;
    
    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code): self
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name): self
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function getAmountOff()
    {
        return $this->amountOff;
    }
    
    public function setAmountOff($amountOff): self
    {
        $this->amountOff    = $amountOff;
        
        return $this;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function setCurrency( CurrencyInterface $currency ): self
    {
        $this->currency = $currency;
        
        return $this;
    }
    
    public function getCurrencyCode()
    {
        if ( $this->currency ) {
            return $this->currency->getCode();
        }
        
        return null;
    }
    
    public function getPercentOff()
    {
        return $this->percentOff;
    }
    
    public function setPercentOff($percentOff): self
    {
        $this->percentOff   = $percentOff;
        
        return $this;
    }
    
    public function getValid(): ?bool
    {
        return $this->enabled;
    }
    
    public function setValid( ?bool $valid ): self
    {
        $this->enabled = (bool) $valid;
        
        return $this;
    }
    
    public function isValid()
    {
        return $this->isEnabled();
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function setType($type): self
    {
        $this->type = $type;
        
        return $this;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    /**
     * @return Collection|OrderInterface[]
     */
    public function getOrders()
    {
        return $this->orders;
    }
    
    public function addOrder( $order )
    {
        if( ! $this->orders->contains( $order ) ) {
            $this->orders->add( $order );
            $order->setCoupon( $this );
            
        }
    }
    
    public function removeOrder( $order )
    {
        if( $this->orders->contains( $order ) ) {
            $this->orders->removeElement( $order );
            $order->setCoupon( null );
        }
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}