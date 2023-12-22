<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

/*
 * 
 * DEPRECATED
 * ==========
 * PayPal Express Checkout is deprecated. Please, use new PayPal Commerce Platform integration.
 * PayPal Commerce Platform: https://github.com/Sylius/PayPalPlugin
 */
class PaypalExpressCheckoutController extends AbstractCheckoutRecurringController
{   
    public function prepareAction( Request $request ): Response
    {
        $cart       = $this->orderFactory->getShoppingCart();
        $payment    = $this->preparePayment( $cart );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_paypal_express_checkout_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function createRecurringPaymentAction( $packagePlanId, Request $request ): Response
    {
        $doneToken = $this->payum->getTokenFactory()->createToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $recurringPayment,
            'vs_payment_paypal_express_checkout_done'
        );
        
        return $this->redirect( $doneToken->getTargetUrl() );
    }
    
    public function cancelRecurringPaymentAction( $paymentId, Request $request ): Response
    {
        
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
        
        $user   = $this->tokenStorage->getToken()->getUser();
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
