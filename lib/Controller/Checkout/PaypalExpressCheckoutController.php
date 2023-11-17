<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
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
        $cart   = $this->orderFactory->getShoppingCart();
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setTotalAmount( $cart->getTotalAmount() );
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->tokenStorage->getToken()->getUser();
        $payment->setClientId( $user ?$user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        $payment->setDetails([
            'PAYMENTREQUEST_0_AMT'          => $cart->getTotalAmount(),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $cart->getCurrencyCode(),
        ]);
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_paypal_express_checkout_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
}
