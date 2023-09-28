<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Vankosoft\PaymentBundle\Component\Payment\Payment as ComponentPayment;

trait PrependPayumTrait
{
    private function prependPayum( ContainerBuilder $container ): void
    {
        if ( ! $container->hasExtension( 'payum' ) ) {
            return;
        }
        
        $vsPaymentConfig    = $container->getExtensionConfig( 'vs_payment' );
        //echo "<pre>"; var_dump($vsPaymentConfig); die;
        $vsPaymentResources = $vsPaymentConfig[0]['resources'];
        
        switch ( $vsPaymentConfig[0]['token_storage'] ) {
            case ComponentPayment::TOKEN_STORAGE_FILESYSTEM:
                $tokenStorageConfig = $this->originalPayumSecurity( $container );
                break;
            case ComponentPayment::TOKEN_STORAGE_DOCTRINE_ORM:
                $tokenStorageConfig = $this->vankosoftPayumSecurity( $vsPaymentResources );
                break;
            default:
                throw new ConfigurationException( 'Unsupported Token Storage !!!' );
        }
        
        $payumConfig        = $container->getExtensionConfig( 'payum' );
        $container->prependExtensionConfig( 'payum', [
            'storages'  => \array_merge( \array_pop( $payumConfig )['storages'] ?? [], [
                $vsPaymentResources['payment']["classes"]["model"] => ['doctrine' => 'orm'],
            ]),
            'security'  =>  $tokenStorageConfig,
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
    
    private function originalPayumSecurity( ContainerBuilder $container )
    {
        $projectRootDir = $container->getParameter( 'kernel.project_dir' );
        $filesystem     = new Filesystem();
        
        if ( ! $filesystem->exists( $projectRootDir . '/var/payum' ) ) {
            $filesystem->mkdir( $projectRootDir . '/var/payum' );
            $filesystem->mkdir( $projectRootDir . '/var/payum/gateways' );
        }
            
        return [
            'token_storage' => [
                'Payum\Core\Model\Token' => [
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
                $vsPaymentResources['payment_token']["classes"]["model"] => ['doctrine' => 'orm'],
            ]
        ];
    }
}
