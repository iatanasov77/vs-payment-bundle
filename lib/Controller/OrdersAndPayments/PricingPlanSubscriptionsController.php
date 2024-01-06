<?php namespace Vankosoft\PaymentBundle\Controller\OrdersAndPayments;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;

class PricingPlanSubscriptionsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        return [
            
        ];
    }
}