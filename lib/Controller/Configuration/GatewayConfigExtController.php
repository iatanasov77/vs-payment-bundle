<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Form\GatewayConfigForm;
use Vankosoft\PaymentBundle\Form\Type\GatewayConfigType;

use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

use Vankosoft\ApplicationBundle\Component\Status;

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
    
    public function indexAction( Request $request ): Response
    {
        return $this->render( '@VSPayment/Pages/GatewayConfigExt/index.html.twig', [
            'items' => $this->gatewayConfigRepository->findAll()
        ]);
    }
    
    public function configAction( $gatewayName, Request $request ): Response
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->getDoctrine()->getManager(), $this->gatewayConfigClass );
        $searchConfig = $gatewayConfigStorage->findBy( ['gatewayName'=>$gatewayName] );
        $gatewayConfig = is_array( $searchConfig ) && isset( $searchConfig[0] ) ? $searchConfig[0] : $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfigForm::class, $gatewayConfig );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            
            $postData                               = $request->request->get( 'gateway_config_form' );
            $submitedConfig                         = $request->request->get( 'gateway_config' );
            $submitedConfig['config']['sandbox']    = false;
            
            // Set Default Config Options From Factory
            $factory = $this->get( 'payum' )->getGatewayFactory( $postData['factoryName'] );
            $config = $factory->createConfig();
            $defaultOptions = $config['payum.default_options'];
            
            if( isset( $defaultOptions['sandbox'] ) ) {
                $submitedConfig['sandboxConfig']['sandbox']   = true;
                $gatewayConfig->setSandboxConfig( $submitedConfig['sandboxConfig'] );
            }
            $gatewayConfig->setConfig( $submitedConfig['config'] );
            
            $gatewayConfigStorage->update( $gatewayConfig );
            
            return $this->redirect( $this->generateUrl( 'vs_payment_gateways_index' ) );
        }
        
        return $this->render('@VSPayment/Pages/GatewayConfigExt/config.html.twig', [
            'gateway'   => $gatewayConfig,
            'form'      => $form->createView()
        ]);
    }
    
    public function gatewayConfigAction( Request $request ): JsonResponse
    {
        $gatewayConfigStorage   = new DoctrineStorage( $this->getDoctrine()->getManager(), $this->gatewayConfigClass );
        $gatewayConfig          = $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfigType::class, [
            'data'      => $gatewayConfig->getConfig( true ),
        ]);
        
        return new JsonResponse([
            'status'        => Status::STATUS_OK,
            
            'gatewayConfig' => $this->render( '@VSPayment/Pages/GatewayConfigExt/config_options.html.twig', [
                'options'   => $this->gatewayConfigOptions( $request->query->get( 'factory' ) ),
                'form'      => $form->createView(),
                'sandbox'   => false,
            ])->getContent(),
            
            'sandboxConfig' => $this->render( '@VSPayment/Pages/GatewayConfigExt/config_options.html.twig', [
                'options'   => $this->gatewayConfigOptions( $request->query->get( 'factory' ) ),
                'form'      => $form->createView(),
                'sandbox'   => true,
            ])->getContent(),
        ]);
    }
    
    private function gatewayConfigOptions( $factory )
    {
        $config    = $this->get( 'payum' )->getGatewayFactory( $factory )->createConfig();
        
        return $config['payum.default_options'];
    }
}
