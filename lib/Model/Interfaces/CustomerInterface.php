<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Customer\Model\CustomerGroupInterface;

/**
 * From: Sylius\Component\Customer\Model\CustomerInterface
 */
interface CustomerInterface
{
    public function getGroup(): ?CustomerGroupInterface;
    public function setGroup( ?CustomerGroupInterface $group ): void;
}