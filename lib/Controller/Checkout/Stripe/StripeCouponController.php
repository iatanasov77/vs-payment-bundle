<?php namespace Vankosoft\PaymentBundle\Controller\Checkout\Stripe;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\CatalogBundle\Model\Interfaces\PricingPlanSubscriptionInterface;

class StripeCouponController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        
    }
}