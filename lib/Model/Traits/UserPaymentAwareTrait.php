<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscriptionInterface;

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
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface", mappedBy="user")
     */
    protected $orders;
    
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface", mappedBy="user")
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
        }
        
        return $this;
    }
    
    public function removePricingPlanSubscription( SubscriptionInterface $pricingPlanSubscription ): self
    {
        if ( $this->pricingPlanSubscriptions->contains( $pricingPlanSubscription ) ) {
            $this->pricingPlanSubscriptions->removeElement( $pricingPlanSubscription );
        }
        
        return $this;
    }
}
