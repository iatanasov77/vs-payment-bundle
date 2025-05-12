<?php namespace Vankosoft\PaymentBundle\Controller\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Form\Stripe\CustomerForm;

class CustomersController extends AbstractController
{
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct( StripeApi $stripeApi )
    {
        $this->stripeApi    = $stripeApi;
    }
    
    public function createCustomerAction( Request $request ): Response
    {
        $form   = $this->createForm( CustomerForm::class, null, ['method' => 'POST'] );
        
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $formData       = $form->getData();
            $this->stripeApi->createCustomer( $formData );
            
            return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
        }
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/create_customer.html.twig', [
            'form'  => $form->createView(),
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function showCustomerPaymentMethods( $customerId, Request $request ): Response
    {
        $availablePaymentMethods    = $this->stripeApi->getPaymentMethods([
            'customer' => $customerId,
        ]);
        
        $data   = $this->templatingEngine->render( '@VSPayment/Pages/GatewayConfig/Stripe/Partial/stripe-payment-methods-table.html.twig', [
            'availablePaymentMethods'   => $availablePaymentMethods,
        ]);
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $data,
        ]);
    }
    
    public function cancelSubscriptionAction( $id, Request $request ): Response
    {
        $this->stripeApi->cancelSubscription( $id );
        
        return $this->redirectToRoute( 'gateway_config_stripe_objects_index' );
    }
}