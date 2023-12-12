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
    
    /** @var OrderItemInterface */
    protected $orderItem;
    
    /** @var \DateTimeInterface */
    protected $expiresAt;
    
    /**
     * This field will store: Subscription Customer and Price Ids
     * to Can Find Subscription Id for Canceling and etc.
     *
     * @var array
     */
    protected $gatewayAttributes;
    
    /** @var bool */
    protected $active = false;
    
    public function __construct()
    {
        $this->gatewayAttributes    = [];
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
    
    public function isForPricingPlan( PricingPlanInterface $pricingPlan ): bool
    {
        return $this->pricingPlan == $pricingPlan;
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
    public function setRecurringPayment( ?bool $recurringPayment )
    {
        $this->recurringPayment = (bool) $recurringPayment;
        
        return $this;
    }
    
    /**
     * For Backward compatibility
     * 
     * {@inheritDoc}
     * @see \Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface::getOrderItems()
     */
    public function getOrderItems()
    {
        $collection = new ArrayCollection();
        $collection->add( $this->orderItem );
        
        return new $collection;
    }
    
    public function getOrderItem(): OrderItemInterface
    {
        return $this->orderItem;
    }
    
    public function setOrderItem(OrderItemInterface $orderItem)
    {
        $this->orderItem    = $orderItem;
        
        return $this;
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
    
    public function getGatewayAttributes()
    {
        return $this->gatewayAttributes ?: [];
    }
    
    public function setGatewayAttributes( array $gatewayAttributes ): self
    {
        $this->gatewayAttributes    = $gatewayAttributes;
        
        return $this;
    }
    
    public function getActive(): bool
    {
        return $this->active;
    }
    
    /**
     * @param bool
     */
    public function setActive( ?bool $active )
    {
        $this->active = (bool) $active;
        
        return $this;
    }
    
    public function isActive(): bool
    {
        return $this->active;
    }
    
    public function isPaid(): bool
    {
        return $this->expiresAt && ( $this->expiresAt > ( new \DateTime() ) );
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