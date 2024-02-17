<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Applicator;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

interface UnitsPromotionAdjustmentsApplicatorInterface
{
    /**
     * @param array|int[] $adjustmentsAmounts
     */
    public function apply( OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts ): void;
}
