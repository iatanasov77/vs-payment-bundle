<?php namespace Vankosoft\PaymentBundle\Controller\General;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class RecievedPaymentsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            
        ];
    }
}