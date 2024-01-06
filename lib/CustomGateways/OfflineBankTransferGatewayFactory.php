<?php namespace Vankosoft\PaymentBundle\CustomGateways;

use Payum\Core\GatewayFactory;
use Payum\Core\Bridge\Spl\ArrayObject;

use Payum\Offline\Action\AuthorizeAction;
use Payum\Offline\Action\CaptureAction;
use Payum\Offline\Action\ConvertPaymentAction;
use Payum\Offline\Action\ConvertPayoutAction;
use Payum\Offline\Action\PayoutAction;
use Payum\Offline\Action\RefundAction;
use Payum\Offline\Action\StatusAction;

use Vankosoft\PaymentBundle\CustomGateways\Keys\OfflineBankTransferKeys;

class OfflineBankTransferGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig( ArrayObject $config )
    {
        $config->defaults([
            'payum.factory_name'            => 'offline_bank_transfer',
            'payum.factory_title'           => 'Offline Bank Transfer',
            
            'payum.action.capture'          => new CaptureAction(),
            'payum.action.authorize'        => new AuthorizeAction(),
            'payum.action.payout'           => new PayoutAction(),
            'payum.action.refund'           => new RefundAction(),
            'payum.action.status'           => new StatusAction(),
            'payum.action.convert_payment'  => new ConvertPaymentAction(),
            'payum.action.convert_payout'   => new ConvertPayoutAction(),
        ]);
        
        if ( false == $config['payum.api'] ) {
            $config['payum.default_options'] = [
                'sandbox'       => true,
                'iban'          => '',
                'bank_name'     => '',
                'reciever_name' => '',
                'reason'        => '',
            ];
            $config->defaults( $config['payum.default_options'] );
            $config['payum.required_options'] = ['iban', 'bank_name', 'reciever_name'];
            
            $config['payum.api'] = function ( ArrayObject $config ) {
                $config->validateNotEmpty( $config['payum.required_options'] );
                
                return new OfflineBankTransferKeys( $config['iban'], $config['bank_name'], $config['reciever_name'], $config['reason'] );
            };
        }
    }
}
