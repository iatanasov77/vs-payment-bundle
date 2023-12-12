<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscriptionInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

trait UserPaymentAwareTrait
{
    /**
     * @var array
     * 
     * @ORM\Column(name="payment_details", type="json")
     */
    protected $paymentDetails   = [];
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $orders;
    
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $pricingPlanSubscriptions;
    
    public function getPaymentDetails(): array
    {
        return $this->paymentDetails;
    }
    
    public function setPaymentDetails( array $paymentDetails ): self
    {
        $this->paymentDetails   = $paymentDetails;
        
        return $this;
    }
    
    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    /**
     * @return Collection|SubscriptionInterface[]
     */
    public function getPricingPlanSubscriptions(): Collection
    {
        return $this->pricingPlanSubscriptions;
    }
    
    public function setPricingPlanSubscriptions( Collection $pricingPlanSubscriptions ): self
    {
        $this->pricingPlanSubscriptions  = $pricingPlanSubscriptions;
        
        return $this;
    }
    
    public function addPricingPlanSubscription( SubscriptionInterface $pricingPlanSubscription ): self
    {
        if ( ! $this->pricingPlanSubscriptions->contains( $pricingPlanSubscription ) ) {
            $this->pricingPlanSubscriptions[]    = $pricingPlanSubscription;
            $pricingPlanSubscription->setUser( $this );
        }
        
        return $this;
    }
    
    public function removePricingPlanSubscription( SubscriptionInterface $pricingPlanSubscription ): self
    {
        if ( $this->pricingPlanSubscriptions->contains( $pricingPlanSubscription ) ) {
            $this->pricingPlanSubscriptions->removeElement( $pricingPlanSubscription );
            $pricingPlanSubscription->setUser( null );
        }
        
        return $this;
    }
    
    /**
     * @return SubscriptionInterface|null
     */
    public function getActivePricingPlanSubscriptionByPlan( PricingPlanInterface $pricingPlan ): ?SubscriptionInterface
    {
        foreach ( $this->pricingPlanSubscriptions as $subscription ) {
            if ( $subscription->isActive() && $subscription->getPricingPlan() == $pricingPlan ) {
                return $subscription;
            }
        }
        
        return null;
    }
    
    /**
     * @return SubscriptionInterface|null
     */
    public function getActivePricingPlanSubscriptionByService( PayedServiceInterface $paidService ): ?SubscriptionInterface
    {
        foreach ( $this->pricingPlanSubscriptions as $subscription ) {
            $thisPaidService    = $subscription->getPricingPlan()->getPaidService()->getPayedService();
            if ( $subscription->isActive() && $thisPaidService == $paidService ) {
                return $subscription;
            }
        }
        
        return null;
    }
}
