<?php namespace Vankosoft\PaymentBundle\Controller\OrdersAndPayments;

use Vankosoft\ApplicationBundle\Controller\AbstractCrudController;
use Symfony\Component\HttpFoundation\Request;
use Vankosoft\PaymentBundle\Form\PaymentFilterForm;

class RecievedPaymentsController extends AbstractCrudController
{
    protected function customData( Request $request, $entity = null ): array
    {
        //$filterFactory  = $request->attributes->get( 'filterFactory' );
        $filterForm     = $this->createForm( PaymentFilterForm::class, null, ['method' => 'POST'] );
        
        return [
            'filterForm'    => $filterForm->createView(),
            //'filterFactory' => $filterFactory,
        ];
    }
}