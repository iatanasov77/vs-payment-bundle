<?php namespace Vankosoft\PaymentBundle\Controller\PaidSubscriptions;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class RecievedPaymentsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $currencies = [];
        if ( $this->resources ) {
            foreach ( $this->resources as $payment ) {
                $currencies[$payment->getCurrencyCode()]   = [
                    'symbol'    => Currencies::getSymbol( $payment->getCurrencyCode() ),
                    'name'      => Currencies::getName( $payment->getCurrencyCode() ),
                ];
            }
        }
        
        return [
            'intlCurrencies'    => $currencies,
        ];
    }
}