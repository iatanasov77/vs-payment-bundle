<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionPeriodInterface;
use Doctrine\Common\Collections\Collection;

interface PricingPlanInterface extends PayableObjectInterface
{
    public function isActive(): bool;
    public function getCategory(): ?PricingPlanCategoryInterface;
    public function getTitle();
    public function getDescription();
    public function isPremium(): bool;
    public function getDiscount(): ?float;
    
    public function getPaidServices(): Collection;
    public function getSubscriptions(): Collection;
}