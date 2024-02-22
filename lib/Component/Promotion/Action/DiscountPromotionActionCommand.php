<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\AdjustmentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;

abstract class DiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    abstract protected function isConfigurationValid( array $configuration ): void;

    public function revert( PromotionSubjectInterface $subject, array $configuration, BasePromotionInterface $promotion ): void
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf( $subject, OrderInterface::class );

        if ( ! $this->isSubjectValid( $subject ) ) {
            return;
        }

        /*
        foreach ( $subject->getItems() as $item ) {
            foreach ( $item->getUnits() as $unit ) {
                $this->removeUnitOrderPromotionAdjustmentsByOrigin( $unit, $promotion );
            }
        }
        */
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function isSubjectValid( PromotionSubjectInterface $subject ): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf( $subject, OrderInterface::class );

        return 0 !== $subject->countItems();
    }

    /*
    private function removeUnitOrderPromotionAdjustmentsByOrigin(
        OrderItemUnitInterface $unit,
        PromotionInterface $promotion,
    ): void {
        foreach ( $unit->getAdjustments( AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT ) as $adjustment ) {
            if ( $promotion->getCode() === $adjustment->getOriginCode() ) {
                $unit->removeAdjustment( $adjustment );
            }
        }
    }
    */
}
