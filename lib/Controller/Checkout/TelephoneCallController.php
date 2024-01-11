<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

class TelephoneCallController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        $cart   = $this->orderFactory->getShoppingCart();
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() );
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->securityBridge->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        // Payment Details
        $paymentDetails   = $this->preparePaymentDetails( $cart );
        $payment->setDetails( $paymentDetails );
        
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_telephone_call_checkout_done'
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    protected function preparePaymentDetails( OrderInterface $cart ): array
    {
        $paymentDetails   = [];
        
        $subscriptions  = $cart->getSubscriptions();
        $hasPricingPlan = ! empty( $subscriptions );
        
        if ( $hasPricingPlan ) {
            $gateway        = $cart->getPaymentMethod()->getGateway();
            $pricingPlan    = $subscriptions[0]->getPricingPlan();
            
            $paymentDetails   = [
                'local' => [
                    'pricing_plan_id'   => $pricingPlan->getId(),
                    'coupon_code'       => '',
                ]
            ];
        }
        
        return $paymentDetails;
    }
}