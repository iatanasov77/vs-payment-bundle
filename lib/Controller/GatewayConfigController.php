<?php namespace Vankosoft\PaymentBundle\Controller;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class GatewayConfigController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
        
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        
    }
}
