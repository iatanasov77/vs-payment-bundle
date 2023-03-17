<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait PaidServiceSubscriptionTrait
{
    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface", mappedBy="paidServiceSubscription")
     */
    protected $orderItems;
    
    /**
     * @return Collection
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }
}
