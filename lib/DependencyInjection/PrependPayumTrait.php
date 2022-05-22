<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;

trait PrependPayumTrait
{
    private function prependPayum( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'payum' ) ) {
            return;
        }
        
        $vsPaymentConfig    = $container->getExtensionConfig( 'vs_payment' );
        $vsPaymentResources = \array_pop( $vsPaymentConfig )['resources'];
        
        $payumConfig        = $container->getExtensionConfig( 'payum' );
        $container->prependExtensionConfig( 'payum', [
            'storages'  => \array_merge( \array_pop( $payumConfig )['storages'] ?? [], [
                $vsPaymentResources['payment']["classes"]["model"] => ['doctrine' => 'orm'],
            ]),
            'security'  =>  $this->originalPayumSecurity(),
            //'security'  =>  $this->vankosoftPayumSecurity( $vsPaymentResources ),
            'dynamic_gateways' => \array_merge( \array_pop( $payumConfig )['dynamic_gateways'] ?? [], [
                'sonata_admin'      => false,
                'config_storage'    => [
                    $vsPaymentResources['gateway_config']["classes"]["model"]   => ['doctrine' => 'orm'],
                ],
            ]),
        ]);

        //$this->debug( $container );
    }
    
    private function debug( ContainerBuilder $container )
    {
        echo '<pre>';
        //var_dump( $container->getParameter( 'vs_payment.model.gateway_config.class' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'vs_payment' ) );
        echo '<br><br><br><br>';
        var_dump( $container->getExtensionConfig( 'payum' ) ); die;
    }
    
    private function originalPayumSecurity()
    {
        return [
            'token_storage' => [
                ['Payum\Core\Model\Token'] => [
                    'filesystem' => [
                        'storage_dir'  => '%kernel.project_dir%/var/payum/gateways',
                        'id_property'  => 'hash',
                    ]
                ],
            ]
        ];
    }
    
    private function vankosoftPayumSecurity( $vsPaymentResources )
    {
        return [
            'token_storage' => [
                $vsPaymentResources['token']["classes"]["model"] => ['doctrine' => 'orm'],
            ]
        ];
    }
}
