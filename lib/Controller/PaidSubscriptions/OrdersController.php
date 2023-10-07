<?php namespace Vankosoft\PaymentBundle\Controller\PaidSubscriptions;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class OrdersController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $currencies = [];
        
        if ( $this->resources ) {
            foreach ( $this->resources as $order ) {
                $currencies[$order->getCurrencyCode()]   = [
                    'symbol'    => Currencies::getSymbol( $order->getCurrencyCode() ),
                    'name'      => Currencies::getName( $order->getCurrencyCode() ),
                ];
            }
        }
        
        return [
            'intlCurrencies'    => $currencies,
        ];
    }
}