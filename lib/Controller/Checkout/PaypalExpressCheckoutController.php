<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 * TEST ACCOUNTS
 * -----------------------------------------------
 * sb-wsp2g401218@personal.example.com / 8o?JWT#6
 * 
 * DEPRECATED
 * ==========
 * PayPal Express Checkout is deprecated. Please, use new PayPal Commerce Platform integration.
 * PayPal Commerce Platform: https://github.com/Sylius/PayPalPlugin
 */
class PaypalExpressCheckoutController extends AbstractCheckoutController
{   
    public function prepareAction( Request $request ): Response
    {
        $card   = $this->getShoppingCard();
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $card );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $card->getCurrencyCode() );
        $payment->setTotalAmount( $card->getTotalAmount() );
        $payment->setDescription( $card->getDescription() );
        
        $payment->setClientId( $this->getUser() ? $this->getUser()->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $this->getUser() ? $this->getUser()->getEmail() : 'UNREGISTERED_USER' );
        
        $payment->setDetails([
            'PAYMENTREQUEST_0_AMT'          => $card->getTotalAmount(),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $card->getCurrencyCode(),
        ]);
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $card->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_paypal_express_checkout_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
}
