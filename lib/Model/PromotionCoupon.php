<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Promotion\Model\PromotionCoupon as BasePromotionCoupon;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionCouponInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;

class PromotionCoupon extends BasePromotionCoupon implements PromotionCouponInterface
{
    /* @var PromotionInterface */
    protected $promotion;
    
    public function getPromotion(): PromotionInterface
    {
        return $this->promotion;
    }
    
    public function setPromotion( PromotionInterface $promotion ): self
    {
        $this->promotion    = $promotion;
        
        return $this;
    }
}