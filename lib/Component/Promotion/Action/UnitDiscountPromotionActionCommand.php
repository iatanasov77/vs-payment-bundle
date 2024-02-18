<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\AdjustmentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;

abstract class UnitDiscountPromotionActionCommand implements PromotionActionCommandInterface
{
    /** @var FactoryInterface */
    protected $adjustmentFactory;
    
    public function __construct( FactoryInterface $adjustmentFactory )
    {
        $this->adjustmentFactory    = $adjustmentFactory;
    }

    /**
     * @throws UnexpectedTypeException
     */
    public function revert( PromotionSubjectInterface $subject, array $configuration, BasePromotionInterface $promotion ): void
    {
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnexpectedTypeException( $subject, OrderInterface::class );
        }

        foreach ( $subject->getItems() as $item ) {
            $this->removeUnitsAdjustment( $item, $promotion );
        }
    }

    protected function removeUnitsAdjustment( OrderItemInterface $item, PromotionInterface $promotion ): void
    {
        foreach ( $item->getUnits() as $unit ) {
            $this->removeUnitOrderItemAdjustments( $unit, $promotion );
        }
    }

    protected function removeUnitOrderItemAdjustments( OrderItemUnitInterface $unit, PromotionInterface $promotion ): void
    {
        foreach ( $unit->getAdjustments( AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT) as $adjustment ) {
            if ( $promotion->getCode() === $adjustment->getOriginCode() ) {
                $unit->removeAdjustment($adjustment);
            }
        }
    }

    protected function addAdjustmentToUnit( OrderItemUnitInterface $unit, int $amount, PromotionInterface $promotion ): void
    {
        if ( ! $this->canPromotionBeApplied( $unit, $promotion ) ) {
            return;
        }

        $adjustment = $this->createAdjustment( $promotion, AdjustmentInterface::ORDER_UNIT_PROMOTION_ADJUSTMENT );

        /** @var OrderItemInterface $orderItem */
        $orderItem      = $unit->getOrderItem();

        /** @var ProductVariantInterface $variant */
        $variant        = $orderItem->getVariant();

        /** @var OrderInterface $order */
        $order          = $orderItem->getOrder();

        $channel        = $order->getChannel();

        //$minimumPrice   = $variant->getChannelPricingForChannel( $channel )->getMinimumPrice();
        $minimumPrice   = 0;
        
        $adjustment->setAmount( $this->calculate( $unit->getTotal(), $minimumPrice, -$amount ) );

        $unit->addAdjustment( $adjustment );
    }

    protected function createAdjustment(
        PromotionInterface $promotion,
        string $type = AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT,
    ): AdjustmentInterface {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentFactory->createNew();
        $adjustment->setType( $type );
        $adjustment->setLabel( $promotion->getName() );
        $adjustment->setOriginCode( $promotion->getCode() );

        return $adjustment;
    }

    private function calculate( int $unitTotal, ?int $minimumPrice, int $promotionAmount ): int
    {
        if ( $unitTotal + $promotionAmount <= $minimumPrice ) {
            return $minimumPrice - $unitTotal;
        }

        return $promotionAmount;
    }

    private function canPromotionBeApplied( OrderItemUnitInterface $unit, PromotionInterface $promotion ): bool
    {
        if ( $promotion->getAppliesToDiscounted() ) {
            return true;
        }

        /** @var OrderItemInterface $item */
        $item       = $unit->getOrderItem();
        $variant    = $item->getVariant();
        if ( $variant === null ) {
            return false;
        }

        /** @var OrderInterface $order */
        $order      = $item->getOrder();

        return $variant->getAppliedPromotionsForChannel( $order->getChannel() )->isEmpty();
    }
}
