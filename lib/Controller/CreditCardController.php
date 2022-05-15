<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\Form\CreditCardForm;

class CreditCardController extends AbstractController
{
    public function showCreditCardFormAction( $paidServiceId, Request $request ): Response
    {
        $form   = $this->getCreditCardForm( $paidServiceId );
        
        return $this->render( '@VSPayment/Pages/CreditCard/credit_card.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    protected function getCreditCardForm( $paidServiceId )
    {
        return $this->createForm( CreditCardForm::class, [
            'paidService' => $paidServiceId,
        ]);
    }
}
