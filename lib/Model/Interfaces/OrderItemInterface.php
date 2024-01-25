<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderItemInterface extends ResourceInterface
{
    public function getOrder();
    public function getPayableObjectType(): string;
    public function getPrice();
    public function getCurrencyCode();
}
