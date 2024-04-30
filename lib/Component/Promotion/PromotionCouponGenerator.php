<?php namespace Vankosoft\PaymentBundle\Component\Promotion;

use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionCouponInterface;

class PromotionCouponGenerator
{
    /** @var PromotionCouponGeneratorInterface */
    protected $syliusPromotionCouponGenerator;
    
    public function __construct( PromotionCouponGeneratorInterface $syliusPromotionCouponGenerator )
    {
        $this->syliusPromotionCouponGenerator   = $syliusPromotionCouponGenerator;
    }
    
    /**
     * @return array|PromotionCouponInterface[]
     */
    public function generate( PromotionInterface $promotion, array $instructionParams  ): array
    {
        $instruction    = new PromotionCouponGeneratorInstruction( $instructionParams );
        
        return $this->syliusPromotionCouponGenerator->generate( $promotion, $instruction );
    }
}