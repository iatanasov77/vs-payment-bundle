<?php namespace Vankosoft\PaymentBundle\Controller;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Symfony\Component\HttpFoundation\Request;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Form\GatewayConfig;
use Vankosoft\PaymentBundle\Form\Type\GatewayConfigType;

use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

class GatewayConfigExtController extends PayumController
{
    /** @var string */
    protected $gatewayConfigClass;
    
    /** @var EntityRepository */
    protected EntityRepository $gatewayConfigRepository;
    
    /** @var Factory */
    protected Factory $gatewayConfigFactory;
    
    public function __construct(
        string $gatewayConfigClass,
        EntityRepository $gatewayConfigRepository,
        Factory $gatewayConfigFactory
    ) {
        $this->gatewayConfigClass       = $gatewayConfigClass;
        $this->gatewayConfigRepository  = $gatewayConfigRepository;
        $this->gatewayConfigFactory     = $gatewayConfigFactory;
    }
    
    public function indexAction( Request $request )
    {
        return $this->render( '@VSPayment/GatewayConfigExt/index.html.twig', [
            'items' => $this->gatewayConfigRepository->findAll()
        ]);
    }
    
    public function configAction( $gatewayName, Request $request )
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->getDoctrine()->getManager(), $this->gatewayConfigClass );
        $searchConfig = $gatewayConfigStorage->findBy( ['gatewayName'=>$gatewayName] );
        $gatewayConfig = is_array( $searchConfig ) && isset( $searchConfig[0] ) ? $searchConfig[0] : $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfig::class, $gatewayConfig );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            
            $postData = $request->request->get( 'gateway_config' );
            
            // Set Default Config Options From Factory
            $factory = $this->get( 'payum' )->getGatewayFactory( $postData['factoryName'] );
            $config = $factory->createConfig();
            $defaultOptions = $config['payum.default_options'];
            
            if( isset( $defaultOptions['sandbox'] ) ) {
                $postData['config']['sandbox']          = false;
                $postData['sandboxConfig']['sandbox']   = true;
                
                $gatewayConfig->setSandboxConfig( $postData['sandboxConfig'] );
            }
            $gatewayConfig->setConfig( $postData['config'] );
            
            $gatewayConfigStorage->update( $gatewayConfig );
            
            return $this->redirect( $this->generateUrl( 'vs_payment_gateways_index' ) );
        }
        
        return $this->render('@VSPayment/GatewayConfigExt/config.html.twig', [
            'gateway'   => $gatewayConfig,
            'form'      => $form->createView()
        ]);
    }
    
    public function gatewayConfigAction( Request $request )
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->getDoctrine()->getManager(), 'Vankosoft\PaymentBundle\Entity\GatewayConfig' );
        $gatewayConfig = $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfigType::class, array('data' => $gatewayConfig->getConfig( false ) ) );
        return $this->render( '@VSPayment/GatewayConfigExt/config_options.html.twig', [
            'options'   => $this->gatewayConfigOptions( $request->query->get( 'factory' ) ),
            'form'      => $form->createView()
        ]);
    }
    
    private function gatewayConfigOptions( $factory )
    {
        $config    = $this->get( 'payum' )->getGatewayFactory( $factory )->createConfig();
        
        return $config['payum.default_options'];
    }
}
