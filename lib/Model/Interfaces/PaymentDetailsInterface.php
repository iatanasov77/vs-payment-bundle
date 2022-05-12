<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface PaymentDetailsInterface extends ResourceInterface
{
    public function getUser();
    public function getPaymentMethod();
    public function getDetails();
    public function getType();
}
