<?php namespace Vankosoft\PaymentBundle\Controller\Checkout\Paypal;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

class PaypalProCheckoutController extends AbstractCheckoutController
{   
    public function prepareAction( Request $request ): Response
    {
        $cart       = $this->orderFactory->getShoppingCart();
        $payment    = $this->preparePayment( $cart );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_paypal_pro_checkout_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    protected function preparePayment( OrderInterface $cart )
    {
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        
        // Maximum length is 127 alphanumeric characters.
        $payment->setDescription( \substr( $cart->getDescription(), 0, 120 ) );
        
        $user   = $this->securityBridge->getUser();
        $payment->setClientId( $user ?$user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        $payment->setDetails([
            'PAYMENTREQUEST_0_AMT'          => $cart->getTotalAmount() * 100,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $cart->getCurrencyCode(),
        ]);
        $storage->update( $payment );
        
        return $payment;
    }
}
