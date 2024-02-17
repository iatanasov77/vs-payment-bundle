<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

use Vankosoft\PaymentBundle\Component\Distributor\MinimumPriceDistributorInterface;
use Vankosoft\PaymentBundle\Component\Distributor\ProportionalIntegerDistributorInterface;
use Vankosoft\PaymentBundle\Component\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;

final class FixedDiscountPromotionActionCommand extends DiscountPromotionActionCommand
{
    public const TYPE = 'order_fixed_discount';

    public function __construct(
        private ProportionalIntegerDistributorInterface $distributor,
        private UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        private ?MinimumPriceDistributorInterface $minimumPriceDistributor = null,
    ) {
    }

    public function execute( PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion ): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf( $subject, OrderInterface::class );

        if ( ! $this->isSubjectValid( $subject ) ) {
            return false;
        }

        $channelCode = $subject->getChannel()->getCode();
        if ( ! isset( $configuration[$channelCode] ) ) {
            return false;
        }

        try {
            $this->isConfigurationValid( $configuration[$channelCode] );
        } catch ( \InvalidArgumentException ) {
            return false;
        }

        $subjectTotal       = $this->getSubjectTotal( $subject, $promotion );
        $promotionAmount    = $this->calculateAdjustmentAmount( $subjectTotal, $configuration[$channelCode]['amount'] );

        if ( 0 === $promotionAmount ) {
            return false;
        }

        if ( $this->minimumPriceDistributor !== null ) {
            $splitPromotion = $this->minimumPriceDistributor->distribute( $subject->getItems()->toArray(), $promotionAmount, $subject->getChannel(), $promotion->getAppliesToDiscounted() );
        } else {
            $itemsTotal = [];
            foreach ( $subject->getItems() as $orderItem ) {
                if ( $promotion->getAppliesToDiscounted() ) {
                    $itemsTotal[] = $orderItem->getTotal();

                    continue;
                }

                $variant = $orderItem->getVariant();
                if (!$variant->getAppliedPromotionsForChannel( $subject->getChannel())->isEmpty() ) {
                    $itemsTotal[] = 0;

                    continue;
                }

                $itemsTotal[] = $orderItem->getTotal();
            }

            $splitPromotion = $this->distributor->distribute( $itemsTotal, $promotionAmount );
        }

        $this->unitsPromotionAdjustmentsApplicator->apply( $subject, $promotion, $splitPromotion );

        return true;
    }

    protected function isConfigurationValid( array $configuration ): void
    {
        Assert::keyExists( $configuration, 'amount' );
        Assert::integer( $configuration['amount'] );
    }

    private function calculateAdjustmentAmount( int $promotionSubjectTotal, int $targetPromotionAmount ): int
    {
        return -1 * min( $promotionSubjectTotal, $targetPromotionAmount );
    }

    private function getSubjectTotal( OrderInterface $order, PromotionInterface $promotion ): int
    {
        return $promotion->getAppliesToDiscounted() ? $order->getPromotionSubjectTotal() : $order->getNonDiscountedItemsTotal();
    }
}
