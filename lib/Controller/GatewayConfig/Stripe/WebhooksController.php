<?php namespace Vankosoft\PaymentBundle\Controller\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Form\Stripe\WebhookEndpointForm;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class WebhooksController extends AbstractController
{
    /** @var Environment */
    private $templatingEngine;
    
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        Environment $templatingEngine,
        StripeApi $stripeApi
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->stripeApi        = $stripeApi;
    }
    
    public function createWebhookEndpointAction( Request $request ): Response
    {
        $form   = $this->createForm( WebhookEndpointForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            if ( empty( $formData['enabled_events'] ) ) {
                throw new \RuntimeException( 'Enabled Events field cannot be empty !!!' );
            }
            
            $this->stripeApi->createWebhookEndpoint( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_webhook_endpoint.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function updateWebhookEndpointAction( $id, Request $request ): Response
    {
        $webhookEndpoint    = $this->stripeApi->retrieveWebhookEndpoint( $id );
        $form               = $this->createForm( WebhookEndpointForm::class, null, [
            'method' => 'POST',
            
            'endpointId'        => $webhookEndpoint['id'],
            'endpointEvents'    => $webhookEndpoint['enabled_events'],
            'endpointUrl'       => $webhookEndpoint['url'],
        ]);
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            if ( empty( $formData['enabled_events'] ) ) {
                throw new \RuntimeException( 'Enabled Events field cannot be empty !!!' );
            }
            
            $this->stripeApi->createWebhookEndpoint( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/update_webhook_endpoint.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function deleteWebhookEndpointAction( $id, Request $request ): Response
    {
        $this->stripeApi->deleteWebhookEndpoint( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
    }
}