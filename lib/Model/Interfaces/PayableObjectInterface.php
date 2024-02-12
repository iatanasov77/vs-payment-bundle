<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;

interface PayableObjectInterface
{
    public function getPrice();
    public function getCurrency(): CurrencyInterface;
    public function getCurrencyCode(): string;
    public function getOrderItems(): Collection;
}
