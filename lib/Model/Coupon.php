<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CouponInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;

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
    protected $amountОff;
    
    /** @var CurrencyInterface|null */
    protected $currency;
    
    /** @var float|null */
    protected $percentOff;
    
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
    
    public function getAmountОff()
    {
        return $this->amountОff;
    }
    
    public function setAmountОff($amountОff): self
    {
        $this->amountОff    = $amountОff;
        
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
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}