<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;

interface AdjustableInterface
{
    /**
     * @return Collection<array-key, AdjustmentInterface>
     */
    public function getAdjustments(?string $type = null): Collection;

    public function addAdjustment(AdjustmentInterface $adjustment): void;

    public function removeAdjustment(AdjustmentInterface $adjustment): void;

    public function getAdjustmentsTotal(?string $type = null): int;

    public function removeAdjustments(?string $type = null): void;

    /**
     * Recalculates adjustments total. Should be used after adjustment change.
     */
    public function recalculateAdjustmentsTotal(): void;
}
