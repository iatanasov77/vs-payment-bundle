<?php namespace Vankosoft\PaymentBundle\Component\Exception;

use RuntimeException;
use Payum\Core\Bridge\Spl\ArrayObject;

class CheckoutException extends RuntimeException
{
    public function __construct ( string $factory, ?ArrayObject $errorModel, $code = null )
    {
        // echo '<pre>'; var_dump( $errorModel ); die;
        switch ( $factory ) {
            case 'stripe_checkout':
            case 'stripe_js':
                $message    = 'STRIPE ERROR: ' . $errorModel['error']['message'];
                break;
            case 'paypal_express_checkout':
                $message    = 'PAYPAL ERROR: ' . $errorModel['L_LONGMESSAGE0'];
                break;
            case 'authorize_net_aim':
                $message    = $errorModel['error_message'];
                break;
            default:
                $message    = "Checkout Error !!!";
        }
        
        parent::__construct( $message, $code );
    }
}