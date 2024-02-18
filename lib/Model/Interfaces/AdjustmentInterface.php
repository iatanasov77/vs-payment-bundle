<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface AdjustmentInterface extends ResourceInterface, TimestampableInterface
{
    public const ORDER_ITEM_PROMOTION_ADJUSTMENT    = 'order_item_promotion';
    public const ORDER_PROMOTION_ADJUSTMENT         = 'order_promotion';
    
    public function getAdjustable(): ?AdjustableInterface;

    public function setAdjustable(?AdjustableInterface $adjustable): void;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getLabel(): ?string;

    public function setLabel(?string $label): void;

    public function getAmount(): int;

    public function setAmount(int $amount): void;

    public function isNeutral(): bool;

    public function setNeutral(bool $neutral): void;

    public function isLocked(): bool;

    public function lock(): void;

    public function unlock(): void;

    /**
     * Adjustments with amount < 0 are called "charges".
     */
    public function isCharge(): bool;

    /**
     * Adjustments with amount > 0 are called "credits".
     */
    public function isCredit(): bool;

    public function getOriginCode(): ?string;

    public function setOriginCode(?string $originCode): void;

    public function getOrder(): ?OrderInterface;

    public function getOrderItem(): ?OrderItemInterface;

    public function getDetails(): array;

    public function setDetails(array $details): void;
}
