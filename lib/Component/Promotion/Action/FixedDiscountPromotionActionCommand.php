<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Action;

use Sylius\Component\Promotion\Model\PromotionInterface as BasePromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Webmozart\Assert\Assert;

use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Component\Distributor\ProportionalIntegerDistributorInterface;
use Vankosoft\PaymentBundle\Component\Promotion\Applicator\UnitsPromotionAdjustmentsApplicatorInterface;
use Vankosoft\CatalogBundle\Component\Distributor\MinimumPriceDistributorInterface;

final class FixedDiscountPromotionActionCommand extends DiscountPromotionActionCommand
{
    public const TYPE = 'order_fixed_discount';

    /** @var ProportionalIntegerDistributorInterface */
    private $distributor;
    
    /** @var UnitsPromotionAdjustmentsApplicatorInterface */
    private $unitsPromotionAdjustmentsApplicator;
    
    /** @var MinimumPriceDistributorInterface | null */
    private $minimumPriceDistributor;
    
    public function __construct(
        ProportionalIntegerDistributorInterface $distributor,
        UnitsPromotionAdjustmentsApplicatorInterface $unitsPromotionAdjustmentsApplicator,
        ?MinimumPriceDistributorInterface $minimumPriceDistributor = null
    ) {
        $this->distributor                          = $distributor;
        $this->unitsPromotionAdjustmentsApplicator  = $unitsPromotionAdjustmentsApplicator;
        $this->minimumPriceDistributor              = $minimumPriceDistributor;
    }

    public function execute( PromotionSubjectInterface $subject, array $configuration, BasePromotionInterface $promotion ): bool
    {
        /** @var OrderInterface $subject */
        Assert::isInstanceOf( $subject, OrderInterface::class );

        if ( ! $this->isSubjectValid( $subject ) ) {
            return false;
        }

        $applicationCode    = $subject->getApplication()->getCode();
        if ( ! isset( $configuration[$applicationCode] ) ) {
            return false;
        }

        try {
            $this->isConfigurationValid( $configuration[$applicationCode] );
        } catch ( \InvalidArgumentException $e ) {
            return false;
        }

        $subjectTotal       = $this->getSubjectTotal( $subject, $promotion );
        $promotionAmount    = $this->calculateAdjustmentAmount( $subjectTotal, $configuration[$applicationCode]['amount'] );

        if ( 0 === $promotionAmount ) {
            return false;
        }

        if ( $this->minimumPriceDistributor !== null ) {
            $splitPromotion = $this->minimumPriceDistributor->distribute( $subject->getItems()->toArray(), $promotionAmount, $subject->getApplication(), $promotion->getAppliesToDiscounted() );
        } else {
            $itemsTotal = [];
            foreach ( $subject->getItems() as $orderItem ) {
                if ( $promotion->getAppliesToDiscounted() ) {
                    $itemsTotal[] = $orderItem->getTotal();

                    continue;
                }

                /*
                $product = $orderItem->getProduct();
                if ( ! $product->getAppliedPromotionsForChannel( $subject->getApplication() )->isEmpty() ) {
                    $itemsTotal[] = 0;

                    continue;
                }
                */

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
