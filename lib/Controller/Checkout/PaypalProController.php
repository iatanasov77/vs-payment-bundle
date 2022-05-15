<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Vankosoft\PaymentBundle\Form\CreditCard as CreditCardForm;

/*
 * TEST CARDS
 * ===========

 Card Type  |	Card Number	    |  Exp. Date  | CVV Code
 --------------------------------------------------------
 Visa       | 4242424242424242  |   Any future| Any 3 
            |                   |      date   | digits
---------------------------------------------------------
 Visa       | 4263982640269299  |   02/2023   |  837
 --------------------------------------------------------
 Visa       | 4263982640269299  |   04/2023   |  738
 
 */

// https://github.com/Payum/Payum/blob/master/docs/symfony/custom-purchase-examples/paypal-pro-checkout.md

// SETUP TEST ACCOUNT MANUEL: https://developer.paypal.com/docs/classic/payflow/test-hosted-pages/#create-a-test-only-payflow-gateway-account

class PaypalProController extends AbstractPaymentMethodController
{
    public function prepareAction( Request $request )
    {
        $ppr = $this->getDoctrine()->getRepository( 'IAUsersBundle:PackagePlan' );
        
        $packagePlanId  = $request->query->get( 'packagePlanId' );
        $packagePlan = $ppr->find( $packagePlanId );
        if ( ! $packagePlan ) {
            throw new \Exception('Invalid Request!!!');
        }
        
        $form = $this->createForm( CreditCardForm::class, null, [
            'action' => $this->generateUrl( 'ia_payment_paypal_prepare' ) . "?packagePlanId=" . $request->query->get( 'packagePlanId' ),
            'method' => 'POST',
        ]);
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $data       = $form->getData();
            $pb         = $this->get( 'ia_payment_builder' );
            $payment    = $pb->buildPayment( $this->getUser(), $packagePlan, $this->gatewayName() );
            
            $payment->setPaymentMethod( 'paypal_express_checkout_NOT_recurring_payment' );
            $payment->setDetails([
                'ACCT'          => $data['acct'],
                'CVV2'          => $data['cvv'],
                'EXPDATE'       => $data['exp_date']->format( 'my' ),
                'AMT'           => $packagePlan->getPrice() * $payment->getCurrencyDivisor(),
                'CURRENCY'      => $packagePlan->getCurrency(),
                'NOSHIPPING'    => 1
            ]);
            $pb->updateStorage( $payment );
            
            $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(
                $this->gatewayName(),
                $payment,
                'ia_payment_paypal_done'
            );
            
            return $this->redirect( $captureToken->getTargetUrl() );
        }

        return $this->render('IAPaymentBundle:PaymentMethod/PaypalPro:prepare.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
    protected function gatewayName()
    {
        return 'paypal_pro_checkout_gateway';
    }
    
    protected function getErrorMessage( $details )
    {
        return 'PAYPAL ERROR: ' . $details['RESPMSG'];
    }
}
