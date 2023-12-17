<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaypalRestController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        $cart   = $this->orderFactory->getShoppingCart();
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setNumber( uniqid() );
        $payment->setTotalAmount( $cart->getTotalAmount() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setDescription( $cart->getDescription() );
        $payment->setOrder( $cart );
        
        $user   = $this->tokenStorage->getToken()->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        /*
        $payment->setDetails([
            'PAYMENTREQUEST_0_AMT'          => $cart->getTotalAmount(),
            'PAYMENTREQUEST_0_CURRENCYCODE' => $cart->getCurrencyCode(),
        ]);
        */
        
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_paypal_rest_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
}