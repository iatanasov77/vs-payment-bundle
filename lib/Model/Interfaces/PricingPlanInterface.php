<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionPeriodInterface;
use Doctrine\Common\Collections\Collection;

interface PricingPlanInterface extends PayableObjectInterface
{
    public function isActive(): bool;
    public function getCategory(): ?PricingPlanCategoryInterface;
    public function getTitle():? string;
    public function getDescription():? string;
    public function isPremium(): bool;
    public function getDiscount():? float;
    
    public function getPaidServicePeriod():? PayedServiceSubscriptionPeriodInterface;
    public function getSubscriptions(): Collection;
}