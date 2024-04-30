<?php namespace Vankosoft\PaymentBundle\Controller\PromotionCoupons;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Promotion\PromotionCouponGenerator;

class CouponsExtController extends AbstractController
{
    /** @var PromotionCouponGenerator */
    protected $generator;
    
    /** @var RepositoryInterface */
    protected $promotionsRepository;
    
    public function __construct( PromotionCouponGenerator $generator, RepositoryInterface $promotionsRepository )
    {
        $this->generator            = $generator;
        $this->promotionsRepository = $promotionsRepository;
    }
    
    public function generateCouponCodeJson( $promotionId, Request $request ): Response
    {
        $promotion          = $this->promotionsRepository->find( $promotionId );
        
        $generatorParams    = [
            'amount'        => 1,   // Number of coupons to generate
            'codeLength'    => 6,
        ];
        
        $coupons        = $this->generator->generate( $promotion, $generatorParams );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'code'      => $coupons[0]->getCode(),
        ]);
    }
}