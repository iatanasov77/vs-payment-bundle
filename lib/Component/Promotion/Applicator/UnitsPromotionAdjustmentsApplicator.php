<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Applicator;

use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionInterface;
//use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\PaymentBundle\Component\Distributor\IntegerDistributorInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\AdjustmentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;
//use Sylius\Component\Order\Model\OrderItemUnitInterface;

final class UnitsPromotionAdjustmentsApplicator implements UnitsPromotionAdjustmentsApplicatorInterface
{
    /** @var AdjustmentFactoryInterface */
    private $adjustmentFactory;
    
    /** @var IntegerDistributorInterface */
    private $distributor;
    
    public function __construct( FactoryInterface $adjustmentFactory, IntegerDistributorInterface $distributor )
    {
        $this->adjustmentFactory    = $adjustmentFactory;
        $this->distributor          = $distributor;
    }

    /**
     * @throws UnsupportedTypeException
     */
    public function apply( OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts ): void
    {
        Assert::eq( $order->countItems(), count( $adjustmentsAmounts ) );

        $i = 0;
        foreach ( $order->getItems() as $item ) {
            $adjustmentAmount = $adjustmentsAmounts[$i++];
            if ( 0 === $adjustmentAmount ) {
                continue;
            }

            //$this->applyAdjustmentsOnItemUnits( $item, $promotion, $adjustmentAmount, $order->getChannel() );
        }
    }

    private function applyAdjustmentsOnItemUnits(
        OrderItemInterface $item,
        PromotionInterface $promotion,
        int $itemPromotionAmount,
        ChannelInterface $channel,
    ): void {
        $splitPromotionAmount = $this->distributor->distribute( $itemPromotionAmount, $item->getQuantity() );

        $variantMinimumPrice = $item->getVariant()->getChannelPricingForChannel( $channel )->getMinimumPrice();

        $i = 0;
        foreach ( $item->getUnits() as $unit ) {
            $promotionAmount = $splitPromotionAmount[$i++];
            if ( 0 === $promotionAmount ) {
                continue;
            }

            $this->addAdjustment(
                $promotion,
                $unit,
                $this->calculate( $unit->getTotal(), $variantMinimumPrice, $promotionAmount ),
            );
        }
    }

    private function addAdjustment( PromotionInterface $promotion, OrderItemUnitInterface $unit, int $amount ): void
    {
        $adjustment = $this->adjustmentFactory
            ->createWithData( AdjustmentInterface::ORDER_PROMOTION_ADJUSTMENT, $promotion->getName(), $amount )
        ;
        $adjustment->setOriginCode( $promotion->getCode() );

        $unit->addAdjustment( $adjustment );
    }

    private function calculate( int $itemTotal, int $minimumPrice, int $promotionAmount ): int
    {
        if ( $itemTotal + $promotionAmount <= $minimumPrice ) {
            return $minimumPrice - $itemTotal;
        }

        return $promotionAmount;
    }
}
