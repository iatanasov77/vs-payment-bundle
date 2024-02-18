<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Promotion\Model\CountablePromotionSubjectInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationRelationInterface;
use AdjustableInterface

interface OrderInterface extends
    ResourceInterface,
    AdjustableInterface,
    ApplicationRelationInterface,
    CountablePromotionSubjectInterface
{
    public function hasRecurringPayment(): bool;
    public function getUser();
    public function getPaymentMethod();
    public function getCoupon();
    public function getPayment();
    public function getTotalAmount();
    public function getCurrencyCode();
    public function getItems();
    
    public function getStatus();
    public function getSessionId();
    
    public function getSubscriptions(): array;
}
