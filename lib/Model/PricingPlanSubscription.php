<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface;
use Vankosoft\UsersSubscriptionsBundle\Component\PayedService\SubscriptionPeriod;

class PricingPlanSubscription implements PricingPlanSubscriptionInterface
{
    /** @var integer */
    protected $id;
    
    /**
     * Relation to the PricingPlan entity
     *
     * @var PricingPlanInterface
     */
    protected $pricingPlan;
    
    /**
     * Relation to the User entity
     *
     * @var SubscribedUserInterface
     */
    protected $user;
    
    /** @var \DateTimeInterface */
    protected $date;
    
    /** @var bool */
    protected $paid = false;
    
    /**
     * @var Collection|OrderInterface[]
     */
    protected $orders;
    
    public function __construct()
    {
        $this->orders   = new ArrayCollection();
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPricingPlan(): PricingPlanInterface
    {
        return $this->pricingPlan;
    }
    
    public function setPricingPlan( PricingPlanInterface $pricingPlan )
    {
        $this->pricingPlan = $pricingPlan;
        
        return $this;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }
    
    public function getDate()
    {
        return $this->date;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    public function isPaid(): bool
    {
        return $this->paid;
    }
    
    public function getPaid(): bool
    {
        return $this->paid;
    }
    
    public function setPaid( $paid )
    {
        $this->paid = $paid;
        
        return $this;
    }
    
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    public function isActive(): bool
    {
        $active     = false;
        $thisDate   = clone $this->date;
        switch( $this->pricingPlan->getPaidService()->getSubscriptionPeriod() ) {
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_UNLIMITED:
                $active = true;
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_YEAR:
                $active = ( $thisDate->add( new \DateInterval( 'P1Y' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_HALFYEAR:
                $active = ( $thisDate->add( new \DateInterval( 'P6M' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_QUARTERYEAR:
                $active = ( $thisDate->add( new \DateInterval( 'P3M' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_MONTH:
                $active = ( $thisDate->add( new \DateInterval( 'P1M' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_SEMIMONTH:
                $active = ( $thisDate->add( new \DateInterval( 'P15D' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_WEEK:
                $active = ( $thisDate->add( new \DateInterval( 'P1W' ) ) ) > ( new \DateTime() );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_DAY:
                $active = ( $thisDate->add( new \DateInterval( 'P1D' ) ) ) > ( new \DateTime() );
                break;
            default:
                $active = false;
        }
        
        return $active;
    }
    
    public function getExpireAt(): ?\DateTime
    {
        $expireAt   = null;
        $thisDate   = clone $this->date;
        switch( $this->pricingPlan->getPaidService()->getSubscriptionPeriod() ) {
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_YEAR:
                $expireAt   = $thisDate->add( new \DateInterval( 'P1Y' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_HALFYEAR:
                $expireAt   = $thisDate->add( new \DateInterval( 'P6M' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_QUARTERYEAR:
                $expireAt   = $thisDate->add( new \DateInterval( 'P3M' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_MONTH:
                $expireAt   = $thisDate->add( new \DateInterval( 'P1M' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_SEMIMONTH:
                $expireAt   = $thisDate->add( new \DateInterval( 'P15D' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_WEEK:
                $expireAt   = $thisDate->add( new \DateInterval( 'P1W' ) );
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_DAY:
                $expireAt   = $thisDate->add( new \DateInterval( 'P1D' ) );
                break;
            default:
                $expireAt   = null;
        }
        
        return $expireAt;
    }
    
    public function getSubscriptionPriority(): ?int
    {
        return $this->pricingPlan ? $this->pricingPlan->getSubscriptionPriority() : null;
    }
    
    public function getPrice()
    {
        return $this->pricingPlan ? $this->pricingPlan->getPrice() : 0.00;
    }
    
    public function getTotalAmount()
    {
        return $this->pricingPlan ? $this->pricingPlan->getPrice() : 0.00;
    }
    
    public function getCurrencyCode()
    {
        return $this->pricingPlan ? $this->pricingPlan->getCurrencyCode() : 'EUR';
    }
}