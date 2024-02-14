<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Promotion\Model\Promotion as BasePromotion;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;

class Promotion extends BasePromotion implements PromotionInterface
{
    /** @var string */
    protected $locale;
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): self
    {
        $this->locale = $locale;
        
        return $this;
    }
}