<?php namespace Vankosoft\PaymentBundle\Controller\General;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class RecievedPaymentsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $currencies = [];
        
        if ( $this->resources ) {
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
}