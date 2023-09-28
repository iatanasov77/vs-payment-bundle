<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionPeriodInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionInterface;

class PricingPlan implements PricingPlanInterface
{
    use ToggleableTrait;    // About enabled field - $enabled (active)
    use TranslatableTrait;
    
    /** @var mixed */
    protected $id;
    
    /** @var PricingPlanCategory */
    protected $category;
    
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $description;
    
    /** @var bool */
    protected $premium = false;
    
    /**
     * In Percents (%)
     * @var float
     */
    protected $discount;
    
    /** @var PayedServiceSubscriptionPeriodInterface */
    protected $paidServicePeriod;
    
    /** @var string */
    protected $locale;
    
    /** @var Collection|OrderItemInterface[] */
    protected $orderItems;
    
    public function __construct()
    {
        $this->orderItems   = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function isActive(): bool
    {
        return $this->isEnabled();
    }
    
    public function getCategory(): ?PricingPlanCategoryInterface
    {
        return $this->category;
    }
    
    public function setCategory( ?PricingPlanCategoryInterface $category ): PricingPlanInterface
    {
        $this->category = $category;
        
        return $this;
    }
    
    public function getTitle():? string
    {
        if( ! $this->title && ! $this->paidServicePeriod ) {
            return null;
        }
        
        return $this->title ?: $this->paidServicePeriod->getTitle();
    }
    
    public function setTitle( $title ): PricingPlanInterface
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getDescription():? string
    {
        if( ! $this->description && ! $this->paidServicePeriod ) {
            return null;
        }
        
        return $this->description ?: $this->paidServicePeriod->getDescription();
    }
    
    public function setDescription( $description ): PricingPlanInterface
    {
        $this->description = $description;
        
        return $this;
    }
    
    public function isPremium(): bool
    {
        return $this->premium;
    }
    
    /**
     * @param bool $enabled
     */
    public function setPremium( ?bool $premium ): PricingPlanInterface
    {
        $this->premium = (bool) $premium;
        
        return $this;
    }
    
    public function getDiscount():? float
    {
        return $this->discount;
    }
    
    public function setDiscount( $discount ): PricingPlanInterface
    {
        $this->discount = $discount;
        
        return $this;
    }
    
    public function getPaidServicePeriod():? PayedServiceSubscriptionPeriodInterface
    {
        return $this->paidServicePeriod;
    }
    
    public function setPaidServicePeriod( PayedServiceSubscriptionPeriodInterface $paidServicePeriod )
    {
        $this->paidServicePeriod  = $paidServicePeriod;
        
        return $this;
    }
    
    /**
     * @return Collection|PayedServiceSubscriptionInterface[]
     */
    public function getSubscriptions(): Collection
    {
        return $this->paidServicePeriod ? $this->paidServicePeriod->getSubscriptions() : [];
    }
    
    public function getPrice()
    {
        return $this->paidServicePeriod ? $this->paidServicePeriod->getPrice() : 0.00;
    }
    
    public function getCurrencyCode()
    {
        return $this->paidServicePeriod ? $this->paidServicePeriod->getCurrencyCode() : 'EUR';
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->locale;
    }
    
    public function setTranslatableLocale($locale): PricingPlanInterface
    {
        $this->locale = $locale;
        
        return $this;
    }
    
    public function getOrderItems()
    {
        return $this->orderItems;
    }
    
    public function getSubscriptionCode(): ?string
    {
        return null;
    }
    
    public function getSubscriptionPriority(): ?int
    {
        return null;
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}