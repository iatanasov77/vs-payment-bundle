<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Symfony\Component\HttpClient\HttplugClient as SymfonyHttplugClient;
use Symfony\Component\HttpClient\HttpClient as SymfonyHttpClient;

use Payum\Core\GatewayFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Vankosoft\PaymentBundle\Component\Payum\Core\MyHttplugClient;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\AuthorizeAction;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\CaptureAction;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\ConvertPaymentAction;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\StatusAction;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\Api\DoLoginAction;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\Api\DoCaptureAction;

/**
 * Title:       24 hours with Phone Call
 * 
 * Description: By calling from a landline €6.51/call, for 1 day
 *              INSTRUCTIONS: Call now, ONLY from a landline at 901 901 4830 and note the code number you will hear.
 *              Enter the code in the Coupon Code field of the site and get access to all movies for 24 hours.
 *              The service is available for calls ONLY from Greece and ONLY from a landline.
 *              Lexitel, charge €6.51 per call (with VAT and 5% landline tax), Help Line 2111885511
 *              
 *              
 *              
 *              
 * Payment by phone/call: https://www.micropayment.ch/products/call2pay/
 *
 */
class TelephoneCallGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig( ArrayObject $config )
    {
        // Dont Verify SSL certificate
        // These should moved in bundle config and disable host verification for DEV Environement Only.
//         $ymfonyHttpClient = new SymfonyHttplugClient( SymfonyHttpClient::create( [
//             "verify_peer"   =>false,
//             "verify_host"   =>false
//         ] ) );
//         $httpClient = new HttplugClient( $ymfonyHttpClient );
        $ymfonyHttpClient = new MyHttplugClient( SymfonyHttpClient::create( [
            "verify_peer"   =>false,
            "verify_host"   =>false
        ] ) );
        
        $config->defaults([
            'payum.factory_name'            => 'telephone_call',
            'payum.factory_title'           => 'Telephone Call',
            
            'payum.action.authorize'        => new AuthorizeAction(),
            'payum.action.capture'          => new CaptureAction(),
            'payum.action.convert_payment'  => new ConvertPaymentAction(),
            'payum.action.status'           => new StatusAction(),
            
            'payum.action.api.do_login'     => new DoLoginAction(),
            'payum.action.api.do_capture'   => new DoCaptureAction(),
        ]);
        
        if ( ! $config['payum.api'] ) {
            $config['payum.default_options'] = [
                'api_login_endpoint'            => '',
                'api_verify_coupon_endpoint'    => '',
                'username'                      => '',
                'password'                      => '',
            ];
            $config->defaults( $config['payum.default_options'] );
            $config['payum.required_options']   = ['api_login_endpoint', 'api_verify_coupon_endpoint', 'username', 'password'];
            
            $config['payum.api'] = function ( ArrayObject $config ) use ( $httpClient )  {
                $config->validateNotEmpty( $config['payum.required_options'] );
                
                $telephoneCallConfig = [
                    'api_login_endpoint'            => $config['api_login_endpoint'],
                    'api_verify_coupon_endpoint'    => $config['api_verify_coupon_endpoint'],
                    'username'                      => $config['username'],
                    'password'                      => $config['password'],
                ];
                
                /*
                 * https://github.com/Payum/PayumBundle/blob/master/UPGRADE.md#20-to-21
                 * payum.http_client service was removed. Use gateway's config to overwrite it.
                 */
                //return new Api( $telephoneCallConfig, $config['payum.http_client'], $config['httplug.message_factory'] );
                return new Api( $telephoneCallConfig, $httpClient, $config['httplug.message_factory'] );
            };
        }
        
        $config['payum.paths'] = array_replace([
            'PayumTelephoneCall'    => __DIR__ . '/Resources/views',
        ], $config['payum.paths'] ?: []);
    }
}
