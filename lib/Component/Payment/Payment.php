<?php namespace Vankosoft\PaymentBundle\Component\Payment;

use Symfony\Component\Routing\RouterInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\GatewayConfigInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;

final class Payment
{
    const TOKEN_STORAGE_FILESYSTEM      = 'filesystem';
    const TOKEN_STORAGE_DOCTRINE_ORM    = 'doctrine_orm';
    
    /** @var RouterInterface */
    private $router;
    
    /** @var OrderFactory */
    private $orderFactory;
    
    public function __construct( RouterInterface $router, OrderFactory $orderFactory )
    {
        $this->router       = $router;
        $this->orderFactory = $orderFactory;
    }
    
    public function getPaymentPrepareRoute( GatewayConfigInterface $gatewayConfig, $isRecurring = false )
    {
        switch( $gatewayConfig->getFactoryName() ) {
            case 'offline':
                $route  = 'vs_payment_offline_prepare';
                break;
            case 'offline_bank_transfer':
                $route  = 'vs_payment_offline_bank_transfer_prepare';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = 'vs_payment_stripe_checkout_prepare';
//                 $route  = $isRecurring ?
//                             'vs_payment_stripe_checkout_recurring_prepare':
//                             'vs_payment_stripe_checkout_prepare';
                break;
            case 'paypal_express_checkout':
                $route  = 'vs_payment_paypal_express_checkout_prepare';
                break;
            case 'paypal_rest':
                $route  = 'vs_payment_paypal_rest_prepare';
                break;
            case 'paypal_pro_checkout':
                $route  = 'vs_payment_paypal_pro_checkout_prepare';
                break;
            case 'paysera':
                $route  = 'vs_payment_paysera_prepare';
                break;
            case 'borica':
                $route  = 'vs_payment_borica_prepare';
                break;
            case 'authorize_net_aim':
                $route  = 'vs_payment_authorize_net_prepare';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
    
    public function getPaymentCreateRecurringRoute( GatewayConfigInterface $gatewayConfig, $isRecurring = false )
    {
        /*  
        $this->router->generate( 'vs_payment_stripe_checkout_create_recurring_payment', [] );
        */
        
        switch( $gatewayConfig->getFactoryName() ) {
            case 'offline':
                $route  = '';
                break;
            case 'offline_bank_transfer':
                $route  = '';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = 'vs_payment_stripe_checkout_prepare';
                break;
            case 'paypal_express_checkout':
                $route  = 'vs_payment_paypal_express_checkout_prepare';
                break;
            case 'paypal_rest':
                $route  = '';
                break;
            case 'paypal_pro_checkout':
                $route  = '';
                break;
            case 'paysera':
                $route  = '';
                break;
            case 'borica':
                $route  = '';
                break;
            case 'authorize_net_aim':
                $route  = '';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
    
    public function getPaymentCancelRecurringRoute( GatewayConfigInterface $gatewayConfig, $isRecurring = false )
    {
        switch( $gatewayConfig->getFactoryName() ) {
            case 'offline':
                $route  = '';
                break;
            case 'offline_bank_transfer':
                $route  = '';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = 'vs_payment_stripe_checkout_prepare';
                break;
            case 'paypal_express_checkout':
                $route  = 'vs_payment_paypal_express_checkout_prepare';
                break;
            case 'paypal_rest':
                $route  = '';
                break;
            case 'paypal_pro_checkout':
                $route  = '';
                break;
            case 'paysera':
                $route  = '';
                break;
            case 'borica':
                $route  = '';
                break;
            case 'authorize_net_aim':
                $route  = '';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
}
