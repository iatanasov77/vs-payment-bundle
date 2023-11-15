<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface;
use Vankosoft\UsersSubscriptionsBundle\Component\PayedService\SubscriptionPeriod;

class PricingPlanSubscription implements PricingPlanSubscriptionInterface
{
    use TimestampableTrait;
    
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
    
    /** @var string */
    protected $code;
    
    /** @var bool */
    protected $recurringPayment = false;
    
    /**
     * @var Collection|OrderInterface[]
     */
    protected $orders;
    
    /** @var \DateTimeInterface */
    protected $expiresAt;
    
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
    
    public function getCode()
    {
        return $this->code;
    }
    
    public function setCode($code)
    {
        $this->code = $code;
        
        return $this;
    }
    
    public function isRecurringPayment(): bool
    {
        return $this->recurringPayment;
    }
    
    public function getRecurringPayment(): bool
    {
        return $this->recurringPayment;
    }
    
    /**
     * @param bool
     */
    public function setRecurringPayment( ?bool $recurringPayment ): PricingPlanInterface
    {
        $this->recurringPayment = (bool) $recurringPayment;
        
        return $this;
    }
    
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }
    
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;
        
        return $this;
    }
    
    public function isPaid(): bool
    {
        return $this->expiresAt && ( $this->expiresAt > ( new \DateTime() ) );
    }
    
    public function isActive(): bool
    {
        return $this->isPaid();
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