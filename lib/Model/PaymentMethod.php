<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

class PaymentMethod implements Interfaces\PaymentMethodInterface
{
    use ToggleableTrait;
    use TranslatableTrait;
    
    /** @var integer */
    protected $id;
    
    /** @var string */
    protected $locale;
    
    /** @var Interfaces\GatewayConfigInterface */
    protected $gateway;
    
    /** @var string */
    protected $slug;
    
    /** @var string */
    protected $name;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getGateway()
    {
        return $this->gateway;
    }
    
    public function setGateway( $gateway )
    {
        $this->gateway  = $gateway;
        
        return $this;
    }
    
    public function getSlug(): ?string
    {
        return $this->slug;
    }
    
    public function setSlug($slug=null): self
    {
        $this->slug = $slug;
        return $this;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    public function getActive(): ?bool
    {
        return $this->enabled;
    }
    
    public function setActive( ?bool $active ): self
    {
        $this->enabled = (bool) $active;
        return $this;
    }
    
    public function isActive()
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
