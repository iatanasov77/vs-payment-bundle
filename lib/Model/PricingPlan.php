<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
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
    
    /** @var Collection|PayedServiceSubscriptionPeriodInterface[] */
    protected $paidServices;
    
    /** @var string */
    protected $locale;
    
    /** @var Collection|OrderItemInterface[] */
    protected $orderItems;
    
    /** @var float */
    protected $price;
    
    /** @var CurrencyInterface */
    protected $currency;
    
    /** @var int */
    protected $subscriptionPriority;
    
    public function __construct()
    {
        $this->paidServices = new ArrayCollection();
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
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle( $title ): PricingPlanInterface
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getDescription()
    {
        return $this->description;
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
    
    public function getPaidServices(): Collection
    {
        return $this->paidServices;
    }
    
    public function setPaidServices( Collection $paidServices )
    {
        $this->paidServices  = $paidServices;
        
        return $this;
    }
    
    public function addPaidService( PayedServiceSubscriptionPeriodInterface $subscriptionPeriod )
    {
        if( ! $this->paidServices->contains( $subscriptionPeriod ) ) {
            $this->paidServices->add( $subscriptionPeriod );
        }
    }
    
    public function removePaidService( PayedServiceSubscriptionPeriodInterface $subscriptionPeriod )
    {
        if( $this->paidServices->contains( $subscriptionPeriod ) ) {
            $this->paidServices->removeElement( $subscriptionPeriod );
        }
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function getCurrencyCode()
    {
        return $this->currency ? $this->currency->getCode() : '';
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
    
    public function getSubscriptionPriority(): ?int
    {
        return $this->subscriptionPriority;
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}