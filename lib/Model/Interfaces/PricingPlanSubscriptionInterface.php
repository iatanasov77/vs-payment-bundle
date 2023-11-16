<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscriptionInterface;

interface PricingPlanSubscriptionInterface extends ResourceInterface, SubscriptionInterface, PayableObjectInterface
{
    public function getPricingPlan(): PricingPlanInterface;
    public function isPaid(): bool;
    public function isActive(): bool;
}