<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface PayableObjectInterface extends ResourceInterface
{
    public function getPrice();
    public function getCurrencyCode();
    public function getOrderItems();
    
    //public function getSubscriptionCode(): ?string;
    //public function getSubscriptionPriority(): ?int;
}
