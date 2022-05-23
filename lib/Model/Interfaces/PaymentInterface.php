<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Payum\Core\Model\PaymentInterface as BasePaymentInterface;
use Payum\Core\Model\DirectDebitPaymentInterface;

interface PaymentInterface extends ResourceInterface, BasePaymentInterface, DirectDebitPaymentInterface
{
    public function getOrder();
}
