<?php namespace Vankosoft\PaymentBundle\Controller\PaidSubscriptions;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Currencies;

class PricingPlanSubscriptionsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        $currencies = [];
        
        if ( $this->classInfo['action'] == 'indexAction' ) {
            foreach ( $this->resources as $subscription ) {
                $currencies[$subscription->getCurrencyCode()]   = [
                    'symbol'    => Currencies::getSymbol( $subscription->getCurrencyCode() ),
                    'name'      => Currencies::getName( $subscription->getCurrencyCode() ),
                ];
            }
        } elseif ( $this->classInfo['action'] == 'showAction' ) {
            foreach ( $entity->getOrderItems() as $orderItem ) {
                $currencies[$subscription->getCurrencyCode()]   = [
                    'symbol'    => Currencies::getSymbol( $orderItem->getOrder()->getCurrencyCode() ),
                    'name'      => Currencies::getName( $orderItem->getOrder()->getCurrencyCode() ),
                ];
            }
        }
        
        return [
            'intlCurrencies'    => $currencies,
        ];
    }
}