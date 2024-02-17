<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Vankosoft\PaymentBundle\Component\Promotion\Filter\FilterInterface;

final class UnitPercentageDiscountPromotionActionCommand extends UnitDiscountPromotionActionCommand
{
    public const TYPE = 'unit_percentage_discount';

    public function __construct(
        FactoryInterface $adjustmentFactory,
        private FilterInterface $taxonFilter,
        private FilterInterface $productFilter,
    ) {
        parent::__construct( $adjustmentFactory );
    }

    public function execute( PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion ): bool
    {
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnexpectedTypeException( $subject, OrderInterface::class );
        }

        $channelCode = $subject->getChannel()->getCode();
        if ( ! isset( $configuration[$channelCode] ) || ! isset( $configuration[$channelCode]['percentage'] ) ) {
            return false;
        }

        $filteredItems = $this->taxonFilter->filter( $subject->getItems()->toArray(), $configuration[$channelCode] );
        $filteredItems = $this->productFilter->filter( $filteredItems, $configuration[$channelCode] );

        if ( empty( $filteredItems ) ) {
            return false;
        }

        foreach ( $filteredItems as $item ) {
            $promotionAmount = (int) round( $item->getUnitPrice() * $configuration[$channelCode]['percentage'] );
            $this->setUnitsAdjustments( $item, $promotionAmount, $promotion );
        }

        return true;
    }

    private function setUnitsAdjustments(
        OrderItemInterface $item,
        int $promotionAmount,
        PromotionInterface $promotion,
    ): void {
        foreach ( $item->getUnits() as $unit ) {
            $this->addAdjustmentToUnit( $unit, $promotionAmount, $promotion );
        }
    }
}
