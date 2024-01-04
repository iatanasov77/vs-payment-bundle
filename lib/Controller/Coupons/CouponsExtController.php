<?php namespace Vankosoft\PaymentBundle\Controller\Coupons;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Iwouldrathercode\SimpleCoupons\Coupon as CouponGenerator;
use Vankosoft\ApplicationBundle\Component\Status;

class CouponsExtController extends AbstractController
{
    public function generateCouponCodeJson( Request $request ): Response
    {
        $codeGenerator  = new CouponGenerator();
        $code           = $codeGenerator->generate();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'code'      => $code,
        ]);
    }
}