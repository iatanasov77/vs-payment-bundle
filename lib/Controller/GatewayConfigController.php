<?php

namespace IA\PaymentBundle\Controller;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Symfony\Component\HttpFoundation\Request;

use IA\PaymentBundle\Entity\GatewayConfig as GatewayConfigEntity;
use IA\PaymentBundle\Form\GatewayConfig;
use IA\PaymentBundle\Form\Type\GatewayConfigType;

use Payum\Core\Bridge\Doctrine\Storage\DoctrineStorage;

class GatewayConfigController extends PayumController
{
    
    public function indexAction( Request $request )
    {
        return $this->render('IAPaymentBundle:GatewayConfig:index.html.twig', [
            'items' => $this->getDoctrine()->getRepository( GatewayConfigEntity::class )->findAll()
        ]);
    }
    
    public function configAction( $gatewayName, Request $request )
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->getDoctrine()->getManager(), 'IA\PaymentBundle\Entity\GatewayConfig' );
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
            
            return $this->redirect( $this->generateUrl( 'ia_payment_gateways_index' ) );
        }
        
        return $this->render('IAPaymentBundle:GatewayConfig:config.html.twig', [
            'gateway'   => $gatewayConfig,
            'form'      => $form->createView()
        ]);
    }
    
    public function gatewayConfigAction( Request $request )
    {
        $gatewayConfigStorage = new DoctrineStorage( $this->getDoctrine()->getManager(), 'IA\PaymentBundle\Entity\GatewayConfig' );
        $gatewayConfig = $gatewayConfigStorage->create();
        
        $form = $this->createForm( GatewayConfigType::class, array('data' => $gatewayConfig->getConfig( false ) ) );
        return $this->render('IAPaymentBundle:GatewayConfig:config_options.html.twig', [
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
