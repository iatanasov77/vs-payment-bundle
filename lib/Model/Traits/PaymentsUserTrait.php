<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\Common\Collections\Collection;

trait PaymentsUserTrait
{
    /**
     * @var Collection
     */
    protected $orders;
    
    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
}
