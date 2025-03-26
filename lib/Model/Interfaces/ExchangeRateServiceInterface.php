<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface ExchangeRateServiceInterface extends ResourceInterface
{
    public function getTitle();
    public function getServiceId();
    public function getOptions();
}
