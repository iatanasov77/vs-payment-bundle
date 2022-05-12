<?php namespace Vankosoft\PaymentBundle\Controller\PaymentMethod;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Request\GetHumanStatus;

/*
 * TEST ACCOUNTS
 * -----------------------------------------------
 * sb-wsp2g401218@personal.example.com / 8o?JWT#6
 */
class PaypalExpressCheckoutController extends AbstractPaymentMethodController
{   
    public function prepareAction( Request $request )
    {
        $ppr = $this->getDoctrine()->getRepository( 'IAUsersBundle:PackagePlan' );
        
        $packagePlan = $ppr->find( $request->query->get( 'packagePlanId' ) );
        if ( ! $packagePlan ) {
            throw new \Exception( 'Invalid Request!!!' );
        }

        if ( $request->isMethod( 'POST' ) ) {
            $pb         = $this->get( 'ia_payment_builder' );
            $payment    = $pb->buildPayment( $this->getUser(), $packagePlan, $this->gatewayName() );
            
            $payment->setPaymentMethod( 'paypal_express_checkout_NOT_recurring_payment' );
            $payment->setDetails([
                'PAYMENTREQUEST_0_AMT'          => $packagePlan->getPrice() * $payment->getCurrencyDivisor(),
                'PAYMENTREQUEST_0_CURRENCYCODE' => $packagePlan->getCurrency(),
                'PAYMENTREQUEST_0_DESC'         => $packagePlan->getDescription(),
                'NOSHIPPING'                    => 1
            ]);
            $pb->updateStorage( $payment );
            
            $captureToken = $this->getPayum()->getTokenFactory()->createCaptureToken(
                $this->gatewayName(), 
                $payment,
                'ia_payment_paypal_express_checkout_done'
            );
            
            return $this->redirect( $captureToken->getTargetUrl() );
        }

        $tplVars = array(
            'formAction'    => $this->generateUrl( 'ia_payment_paypal_express_checkout_prepare' ) . '?packagePlanId=' . $packagePlan->getId(),
            'packagePlan'   => $packagePlan
        );
        return $this->render('IAPaymentBundle:PaymentMethod/PaypalExpressCheckout:CheckoutForm.html.twig', $tplVars);
    }
    
    protected function gatewayName()
    {
        return 'paypal_express_checkout_gateway';
    }
    
    protected function getErrorMessage( $details )
    {
        return 'PAYPAL ERROR: ' . $details['L_LONGMESSAGE0'];
    }
}
