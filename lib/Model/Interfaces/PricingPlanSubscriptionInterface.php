<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscriptionInterface;

interface PricingPlanSubscriptionInterface extends ResourceInterface, SubscriptionInterface
{
    public function getPayedService();
    public function isActive(): bool;
}