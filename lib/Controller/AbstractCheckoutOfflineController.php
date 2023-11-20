<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCheckoutOfflineController extends AbstractCheckoutController
{
    abstract public function getInfo( Request $request ): Response;
}