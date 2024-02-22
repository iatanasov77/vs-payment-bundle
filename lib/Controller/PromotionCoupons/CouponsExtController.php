<?php namespace Vankosoft\PaymentBundle\Controller\PromotionCoupons;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Promotion\PromotionCouponGenerator;

class CouponsExtController extends AbstractController
{
    /** @var PromotionCouponGenerator */
    private $generator;
    
    public function __construct( PromotionCouponGenerator $generator )
    {
        $this->generator    = $generator;
    }
    
    public function generateCouponCodeJson( $promotionId, Request $request ): Response
    {
        $generatorParams    = [
            'amount'        => 1,   // Number of coupons to generate
            'codeLength'    => 6,
        ];
        $coupons        = $this->generator->generate( $generatorParams );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'code'      => $coupons[0]->getCode(),
        ]);
    }
}