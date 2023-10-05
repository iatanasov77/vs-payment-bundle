<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait PaymentsUserTrait
{    
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
    
    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
    
    /**
     * @return Collection
     */
    public function getPricingPlanSubscriptions(): Collection
    {
        return $this->pricingPlanSubscriptions;
    }
}
