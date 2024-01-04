<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;

interface CouponInterface extends ResourceInterface
{
    public function getCode();
    public function getName();
    public function getAmountOff();
    public function getPercentOff();
}
