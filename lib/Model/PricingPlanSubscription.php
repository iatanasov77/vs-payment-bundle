<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
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
    
    /** @var bool */
    protected $recurringPayment = false;
    
    /** @var Collection|OrderItemInterface[] */
    protected $orderItems;
    
    /** @var \DateTimeInterface */
    protected $expiresAt;
    
    public function __construct()
    {
        $this->orderItems   = new ArrayCollection();
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
    
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
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
    
    public function getCode(): ?string
    {
        return $this->pricingPlan ? $this->pricingPlan->getSubscriptionCode() : null;
    }
    
    public function getSubscriptionCode(): ?string
    {
        return $this->pricingPlan ? $this->pricingPlan->getSubscriptionCode() : null;
    }
    
    public function getServiceCode(): ?string
    {
        return $this->pricingPlan ? $this->pricingPlan->getServiceCode() : null;
    }
    
    public function getPeriodCode(): ?string
    {
        return $this->pricingPlan ? $this->pricingPlan->getPeriodCode() : null;
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