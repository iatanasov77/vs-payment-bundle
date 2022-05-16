<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Model\DirectDebitPaymentInterface;

interface PaymentInterface extends ResourceInterface, PaymentInterface, DirectDebitPaymentInterface
{
    public function getUser();
    public function getPaidServicePeriod();
    public function getPaymentMethod();
}
