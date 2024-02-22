<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Applicator;

use Sylius\Component\Promotion\Model\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

interface UnitsPromotionAdjustmentsApplicatorInterface
{
    /**
     * @param array|int[] $adjustmentsAmounts
     */
    public function apply( OrderInterface $order, PromotionInterface $promotion, array $adjustmentsAmounts ): void;
}
