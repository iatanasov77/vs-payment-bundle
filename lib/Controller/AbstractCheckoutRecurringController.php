<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractCheckoutRecurringController extends AbstractCheckoutController
{
    abstract public function createRecurringPaymentAction( $packagePlanId, Request $request );
    
    abstract public function cancelAction( $paymentId, Request $request );
}
