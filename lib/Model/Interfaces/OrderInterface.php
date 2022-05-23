<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface OrderInterface extends ResourceInterface
{
    public function getUser();
    public function getPaymentMethod();
    public function getPayment();
    public function getTotalAmount();
    public function getCurrencyCode();
    public function getItems();
}
