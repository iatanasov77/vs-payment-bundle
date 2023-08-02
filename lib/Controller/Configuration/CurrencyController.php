<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class CurrencyController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $currencies = [];
        
        if ( \is_array( $this->resources ) ) {
            foreach ( $this->resources as $currency ) {
                $currencies[$currency->getCode()]   = [
                    'symbol'    => Currencies::getSymbol( $currency->getCode() ),
                    'name'      => Currencies::getName( $currency->getCode() ),
                ]; 
            }
        }
        
        return [
            'intlCurrencies'    => $currencies,
        ];
    }
    
    protected function prepareEntity( &$entity, &$form, Request $request )
    {
        
    }
}
