<?php namespace Vankosoft\PaymentBundle\Model\Traits;

trait PayableObjectTrait
{
    protected $orderItems;
    
    public function getOrderItems()
    {
        return $this->orderItems;
    }
}
