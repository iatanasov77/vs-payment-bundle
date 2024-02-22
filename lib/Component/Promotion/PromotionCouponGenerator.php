<?php namespace Vankosoft\PaymentBundle\Component\Promotion;

use Symfony\Component\Form\FormInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PromotionCouponInterface;

class PromotionCouponGenerator
{
    protected $syliusPromotionCouponGenerator;
    
    public function __construct( PromotionCouponGeneratorInterface $syliusPromotionCouponGenerator )
    {
        $this->syliusPromotionCouponGenerator   = $syliusPromotionCouponGenerator;
    }
    
    /**
     * @return array|PromotionCouponInterface[]
     */
    public function generate( PromotionInterface $promotion, FormInterface $form  ): array
    {
        //$instruction    = $form->getData();
        $instruction    = new PromotionCouponGeneratorInstruction( $form );
        
        return $this->syliusPromotionCouponGenerator->generate( $promotion, $instruction );
    }
}