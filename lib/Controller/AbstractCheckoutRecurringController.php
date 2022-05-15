<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Payum\Core\Request\GetHumanStatus;

abstract class AbstractCheckoutRecurringController extends AbstractCheckoutController
{
    
    abstract public function createRecurringPaymentAction( $packagePlanId, Request $request );
    
    abstract public function cancelAction( $paymentId, Request $request );
}
