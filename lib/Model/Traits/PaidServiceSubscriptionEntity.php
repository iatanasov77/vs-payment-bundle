<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;

trait PaidServiceSubscriptionEntity
{
    /** @var Collection | OrderItem[] */
    #[ORM\OneToMany(targetEntity: OrderItemInterface::class, mappedBy: "paidServiceSubscription")]
    protected $orderItems;
    
    /**
     * @return Collection
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }
}
