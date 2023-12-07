<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Doctrine\Common\Comparable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionPeriodInterface;
use Vankosoft\UsersSubscriptionsBundle\Component\PayedService\SubscriptionPeriod;
use Vankosoft\PaymentBundle\Component\Exception\PricingPlanException;

class PricingPlan implements PricingPlanInterface, Comparable
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
    protected $paidService;
    
    /** @var string */
    protected $locale;
    
    /** @var float */
    protected $price;
    
    /** @var CurrencyInterface */
    protected $currency;
    
    /** @var Collection|PricingPlanSubscriptionInterface[] */
    protected $subscriptions;
    
    /**
     * This field will store: Subscription Plan Ids, etc.
     * 
     * @var array
     */
    protected $gatewayAttributes;
    
    public function __construct()
    {
        $this->subscriptions        = new ArrayCollection();
        $this->gatewayAttributes    = [];
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
    
    public function getDiscount(): ?float
    {
        return $this->discount;
    }
    
    public function setDiscount( $discount ): PricingPlanInterface
    {
        $this->discount = $discount;
        
        return $this;
    }
    
    public function getPaidService(): ?PayedServiceSubscriptionPeriodInterface
    {
        return $this->paidService;
    }
    
    public function setPaidService( PayedServiceSubscriptionPeriodInterface $paidService )
    {
        $this->paidService  = $paidService;
        
        return $this;
    }
    
    public function getTotalAmount()
    {
        return $this->price;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function setPrice($price)
    {
        $this->price    = $price;
        return $this;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function setCurrency($currency)
    {
        $this->currency = $currency;
        return $this;
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
    
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }
    
    public function addSubscription( PricingPlanSubscriptionInterface $subscription ): self
    {
        if ( ! $this->subscriptions->contains( $subscription ) ) {
            $this->subscriptions[] = $subscription;
            $subscription->setPricingPlan( $this );
        }
        
        return $this;
    }
    
    public function removeSubscription( PricingPlanSubscriptionInterface $subscription ): self
    {
        if ( $this->subscriptions->contains( $subscription ) ) {
            $this->subscriptions->removeElement( $subscription );
            $subscription->setPricingPlan( null );
        }
        
        return $this;
    }
    
    public function getGatewayAttributes()
    {
        return $this->gatewayAttributes;
    }
    
    public function setGatewayAttributes( array $gatewayAttributes ): self
    {
        $this->gatewayAttributes    = $gatewayAttributes;
        
        return $this;
    }
    
    public function getServiceCode(): ?string
    {
        return $this->paidService->getPayedService()->getSubscriptionCode();
    }
    
    public function getPeriodCode(): ?string
    {
        return $this->paidService->getPaidServicePeriodCode();
    }
    
    public function getSubscriptionCode(): ?string
    {
        return $this->getServiceCode() . '-' . $this->getPeriodCode();
    }
    
    /**
     * @TODO Need to be removed. Use Compare Method.
     * 
     * @return int|NULL
     */
    public function getSubscriptionPriority(): ?int
    {
        return $this->paidService->getPayedService()->getSubscriptionPriority();
    }
    
    public function getSubscriptionPeriod(): \DateInterval
    {
        $period = null;
        
        switch( $this->paidService->getSubscriptionPeriod() ) {
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_UNLIMITED:
                $period = new \DateInterval( 'P1000Y' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_YEAR:
                $period = new \DateInterval( 'P1Y' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_HALFYEAR:
                $period = new \DateInterval( 'P6M' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_QUARTERYEAR:
                $period = new \DateInterval( 'P3M' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_MONTH:
                $period = new \DateInterval( 'P1M' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_SEMIMONTH:
                $period = new \DateInterval( 'P15D' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_WEEK:
                $period = new \DateInterval( 'P1W' );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_DAY:
                $period = new \DateInterval( 'P1D' );
                break;
            default:
                throw new PricingPlanException( 'Unknown Pricing Plan Subscription Period' );
        }
        
        return $period;
    }
    
    /**
     * {@inheritDoc}
     * @see \Doctrine\Common\Comparable::compareTo($other)
     */
    public function compareTo( $other ): int
    {
        if ( $this->getServiceCode() != $other->getServiceCode() ) {
            throw new \Exception( 'These Pricing Plans are Not Comparable !!!' );
        }
        
        $dateRef = new \DateTimeImmutable();
        if ( $dateRef->add( $this->getSubscriptionPeriod() ) > $dateRef->add( $other->getSubscriptionPeriod() ) ) {
            return 1;
        } elseif ( $dateRef->add( $this->getSubscriptionPeriod() ) < $dateRef->add( $other->getSubscriptionPeriod() ) ) {
            return -1;
        }
        
        return 0;
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}