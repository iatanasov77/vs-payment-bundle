<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderItemInterface extends ResourceInterface
{
    public function getOrder();
    public function getSubscription();
    public function getProduct();
    public function getPayableObjectType();
    public function getPrice();
    public function getCurrencyCode();
    public function getObject(): PayableObjectInterface;
}
