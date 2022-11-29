<?php namespace Vankosoft\PaymentBundle\Component\Payment;

use Payum\Core\Model\GatewayConfigInterface;

class Payment
{
    public function getPaymentPrepareRoute( GatewayConfigInterface $gatewayConfig )
    {
        $route  = 'not_configured';
        
        switch( $gatewayConfig->getFactoryName() ) {
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = 'vs_payment_stripe_checkout_prepare';
                
                break;
            case 'paypal_express_checkout':
                $route  = 'vs_payment_paypal_express_checkout_prepare';
                
                break;
            case 'paypal_rest':
                $route  = 'vs_payment_paypal_rest_prepare';
                
                break;
            default:
                
                
        }
        
        return $route;
    }
}