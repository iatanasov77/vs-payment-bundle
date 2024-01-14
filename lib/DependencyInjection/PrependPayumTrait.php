<?php namespace Vankosoft\PaymentBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Payum\Core\Storage\FilesystemStorage;
use Vankosoft\PaymentBundle\Component\Payment\Payment as ComponentPayment;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallGatewayFactory;

/*
 * Payum Symfony Configuration
 * ===========================
 * https://github.com/Payum/Payum/blob/master/docs/symfony/configuration-reference.md
 */
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
        
        $projectRootDir     = $container->getParameter( 'kernel.project_dir' );
        $tokenStorageConfig = $this->_createTokenStorageConfig( $container, $vsPaymentConfig, $vsPaymentResources );
        $payumConfig        = $container->getExtensionConfig( 'payum' );
        $container->prependExtensionConfig( 'payum', [
            'storages'          => $this->_createStoragesConfig( $payumConfig, $vsPaymentResources, $projectRootDir ),
            'security'          => $tokenStorageConfig,
            'gateways'          => $this->_createCoreGatewayConfig( $payumConfig ),
            'dynamic_gateways'  => $this->_createDynamicGatewaysConfig( $payumConfig, $vsPaymentResources ),
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
    
    private function _createStoragesConfig( array $payumConfig, array $vsPaymentResources, string $projectRootDir ): array
    {
        return \array_merge( \array_pop( $payumConfig )['storages'] ?? [], [
            $vsPaymentResources['payment']["classes"]["model"]      => ['doctrine' => 'orm'],
            
            // PayPal Recurring Payment Models
            'Vankosoft\PaymentBundle\Model\PayPal\AgreementDetails' => ['filesystem' => [
                'storage_dir'   => $projectRootDir . '/var/payum/storage',
                'id_property'   => 'payum_id',
            ]
            ],
            'Vankosoft\PaymentBundle\Model\PayPal\RecurringPaymentDetails' => ['filesystem' => [
                'storage_dir'   => $projectRootDir . '/var/payum/storage',
                'id_property'   => 'payum_id',
            ]
            ],
        ]);
    }
    
    private function _createDynamicGatewaysConfig( array $payumConfig, array $vsPaymentResources ): array
    {
        return \array_merge( \array_pop( $payumConfig )['dynamic_gateways'] ?? [], [
            'sonata_admin'      => false,
            'config_storage'    => [
                $vsPaymentResources['gateway_config']["classes"]["model"]   => ['doctrine' => 'orm'],
            ],
        ]);
    }
    
    private function _createTokenStorageConfig( ContainerBuilder $container, array $vsPaymentConfig, array $vsPaymentResources ): array
    {
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
        
        return $tokenStorageConfig;
    }
    
    private function _createCoreGatewayConfig( array $payumConfig ): array
    {
        return \array_merge( \array_pop( $payumConfig )['gateways'] ?? [], [
            'core'    => [
                'payum.template.obtain_coupon_code' => '@PayumTelephoneCall/obtain_coupon_code.html.twig',
//                 'payum.paths' => \array_merge( $payumConfig['gateways']['core']['payum.paths'], [
//                     'PayumTelephoneCall' => dirname( ( new \ReflectionClass( TelephoneCallGatewayFactory::class ) )->getFileName() ) . '/Resources/views',
//                 ]),
                'payum.paths' => [
                    'PayumTelephoneCall' => dirname( ( new \ReflectionClass( TelephoneCallGatewayFactory::class ) )->getFileName() ) . '/Resources/views',
                ],
            ],
        ]);
    }
}
