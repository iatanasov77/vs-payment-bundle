<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
use Vankosoft\PaymentBundle\Component\Promotion\Filter\FilterInterface;

final class UnitFixedDiscountPromotionActionCommand extends UnitDiscountPromotionActionCommand
{
    public const TYPE = 'unit_fixed_discount';

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
        if ( ! isset( $configuration[$channelCode] ) ) {
            return false;
        }

        $amount = $configuration[$channelCode]['amount'];
        if ( 0 === $amount ) {
            return false;
        }
        
        $filteredItems = $this->taxonFilter->filter( $subject->getItems()->toArray(), $configuration[$channelCode] );
        $filteredItems = $this->productFilter->filter( $filteredItems, $configuration[$channelCode] );

        if ( empty( $filteredItems ) ) {
            return false;
        }

        foreach ( $filteredItems as $item ) {
            $this->setUnitsAdjustments( $item, $amount, $promotion );
        }

        return true;
    }

    private function setUnitsAdjustments( OrderItemInterface $item, int $amount, PromotionInterface $promotion ): void
    {
        foreach ( $item->getUnits() as $unit ) {
            $this->addAdjustmentToUnit(
                $unit,
                min( $unit->getTotal(), $amount ),
                $promotion,
            );
        }
    }
}
