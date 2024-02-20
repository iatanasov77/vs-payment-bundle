<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Webmozart\Assert\Assert;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\Component\Promotion\Model\Promotion as BasePromotion;
use Sylius\Component\Promotion\Model\PromotionTranslationInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;

class Promotion extends BasePromotion implements PromotionInterface
{
    use TranslatableTrait {
        TranslatableTrait::__construct as private initializeTranslationsCollection;
    }
    
    /** @var string */
    protected $locale;
    
    /** @var Collection<array-key, PromotionInterface> */
    protected $applications;
    
    public function __construct()
    {
        parent::__construct();
        $this->initializeTranslationsCollection();
        
        /** @var ArrayCollection<array-key, ApplicationInterface> $this->applications */
        $this->applications = new ArrayCollection();
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
    
    public function getApplications(): Collection
    {
        /** @phpstan-ignore-next-line */
        return $this->applications;
    }
    
    public function addApplication(ApplicationInterface $application): void
    {
        Assert::isInstanceOf($application, ApplicationInterface::class);
        if (!$this->hasApplication($application)) {
            $this->applications->add($application);
        }
    }
    
    public function removeApplication(ApplicationInterface $application): void
    {
        Assert::isInstanceOf($application, ApplicationInterface::class);
        if ($this->hasApplication($application)) {
            $this->applications->removeElement($application);
        }
    }
    
    public function hasApplication(ApplicationInterface $application): bool
    {
        return $this->applications->contains($application);
    }
    
    public function setRules( Collection $rules )
    {
        $this->rules    = $rules;
    }
    
    public function setActions( Collection $actions )
    {
        $this->actions  = $actions;
    }
    
    /** @return PromotionTranslationInterface */
    public function getTranslation( ?string $locale = null ): TranslationInterface
    {
        /** @var PromotionTranslationInterface $translation */
        $translation = $this->doGetTranslation( $locale );
        
        return $translation;
    }
    
    protected function createTranslation(): PromotionTranslationInterface
    {
        //return new PromotionTranslation();
    }
}