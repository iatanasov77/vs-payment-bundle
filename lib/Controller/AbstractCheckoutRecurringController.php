<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCheckoutRecurringController extends AbstractCheckoutController
{
    abstract public function createRecurringPaymentAction( $packagePlanId, Request $request ): Response;
    
    abstract public function cancelAction( $paymentId, Request $request ): Response;
}
