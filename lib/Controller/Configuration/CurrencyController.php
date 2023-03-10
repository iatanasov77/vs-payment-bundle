<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class CurrencyController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            intlCurrencies  => Currencies,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        
    }
}
