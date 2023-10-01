<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Payum;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Form\GatewayConfigForm;
use Vankosoft\PaymentBundle\Form\Type\GatewayConfigType;

use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

use Vankosoft\ApplicationBundle\Component\Status;

/**
 * How can I create a payment collection project ... - Paysera
 * To Can Get ProjectId and Sign Password For Paysera
 * =====================================================================
 * https://support.paysera.com/index.php?/payseraeng/Knowledgebase/Article/View/1813/135/1003-how-can-i-create-a-payment-collection-project-for-a-paysera-tickets-service
 * 
 * 
 * Виртуален ПОС - BORICA
 * =====================================================================
 * https://webops.eu/blog/%D0%B2%D0%B8%D1%80%D1%82%D1%83%D0%B0%D0%BB%D0%B5%D0%BD-%D0%BF%D0%BE%D1%81-vpos-%D0%BE%D1%82-%D0%B1%D0%BE%D1%80%D0%B8%D0%BA%D0%B0/
 * https://www.postbank.bg/bg-BG/Malak-biznes/POS/Virtual-Pos
 *
 */
class GatewayConfigExtController extends PayumController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var string */
    protected $gatewayConfigClass;
    
    /** @var RepositoryInterface */
    protected RepositoryInterface $gatewayConfigRepository;
    
    /** @var Factory */
    protected Factory $gatewayConfigFactory;
    
    public function __construct(
        Payum $payum,
        ManagerRegistry $doctrine,
        string $gatewayConfigClass,
        RepositoryInterface $gatewayConfigRepository,
        Factory $gatewayConfigFactory
    ) {
        parent::__construct( $payum );
        
        $this->doctrine                 = $doctrine;
        $this->gatewayConfigClass       = $gatewayConfigClass;
        $this->gatewayConfigRepository  = $gatewayConfigRepository;
        $this->gatewayConfigFactory     = $gatewayConfigFactory;
    }
    
    /** @NOTE This Action Not Used Anymore */
    public function indexAction( Request $request ): Response
    {
        return $this->render( '@VSPayment/Pages/GatewayConfigExt/index.html.twig', [
            'items' => $this->gatewayConfigRepository->findAll()
        ]);
    }
    
    public function configAction( $gatewayName, Request $request ): Response
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->doctrine->getManager(), $this->gatewayConfigClass );
        $searchConfig = $gatewayConfigStorage->findBy( ['gatewayName'=>$gatewayName] );
        $gatewayConfig = is_array( $searchConfig ) && isset( $searchConfig[0] ) ? $searchConfig[0] : $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfigForm::class, $gatewayConfig );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em                                     = $this->doctrine->getManager();
            $submitedGatewayConfig                  = $form->getData();
            $postData                               = $request->request->all( 'gateway_config_form' );
            //echo "<pre>"; var_dump( $postData ); die;
            
            if ( $submitedGatewayConfig->getFactoryName() != 'offline' ) {
                $submitedGatewayConfig->setConfig( $postData['config'] ) ;
                $submitedGatewayConfig->setSandboxConfig( $postData['sandboxConfig'] ) ;
            }
            
            $em->persist( $submitedGatewayConfig );
            $em->flush();
            
            return $this->redirect( $this->generateUrl( 'vs_payment_gateway_config_index' ) );
        }
        
        return $this->render('@VSPayment/Pages/GatewayConfigExt/config.html.twig', [
            'gateway'   => $gatewayConfig,
            'factory'   => $gatewayConfig->getFactoryName(),
            'form'      => $form->createView()
        ]);
    }
    
    public function gatewayConfigAction( Request $request ): JsonResponse
    {
        $gatewayConfigStorage   = new DoctrineStorage( $this->doctrine->getManager(), $this->gatewayConfigClass );
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
                'factory'   => $request->query->get( 'factory' ),
            ])->getContent(),
            
            'sandboxConfig' => $this->render( '@VSPayment/Pages/GatewayConfigExt/config_options.html.twig', [
                'options'   => $this->gatewayConfigOptions( $request->query->get( 'factory' ) ),
                'form'      => $form->createView(),
                'sandbox'   => true,
                'factory'   => $request->query->get( 'factory' ),
            ])->getContent(),
        ]);
    }
    
    private function gatewayConfigOptions( $factory )
    {
        $config             = $this->payum->getGatewayFactory( $factory )->createConfig();
        $payumFactoryConfig = $config['payum.default_options'];
        //var_dump( $payumFactoryConfig ); die;
        
        if ( $factory == 'paypal_rest' ) {
            // The key 'config' is array and not needed use 'config_path' and set path to the ini file
            // Examle ini file: https://github.com/paypal/PayPal-PHP-SDK/blob/master/sample/sdk_config.ini
            unset( $payumFactoryConfig['config'] );
        }
        
        return $payumFactoryConfig;
    }
}
