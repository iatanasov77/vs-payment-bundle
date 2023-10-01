<?php namespace Vankosoft\PaymentBundle\Component\Payment;

use Payum\Core\Model\GatewayConfigInterface;

class Payment
{
    const TOKEN_STORAGE_FILESYSTEM      = 'filesystem';
    const TOKEN_STORAGE_DOCTRINE_ORM    = 'doctrine_orm';
    
    public function getPaymentPrepareRoute( GatewayConfigInterface $gatewayConfig )
    {
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
            case 'paysera':
                $route  = 'vs_payment_paysera_prepare';
                break;
            case 'borica':
                $route  = 'vs_payment_borica_prepare';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
}
