<?php namespace Vankosoft\PaymentBundle\Controller\GatewayConfig\Stripe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class StripeObjectsController extends AbstractController
{
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct( StripeApi $stripeApi )
    {
        $this->stripeApi    = $stripeApi;
    }
    
    public function indexAction( Request $request ): Response
    {
        $availablePlans             = $this->stripeApi->getPlans();
        $availableProducts          = $this->stripeApi->getProducts();
        $availablePrices            = $this->stripeApi->getPrices();
        $availableCustomers         = $this->stripeApi->getCustomers();
        $availablePaymentMethods    = $this->stripeApi->getPaymentMethods();
        $availableSubscriptions     = $this->stripeApi->getSubscriptions();
        $availableConnectedAccounts = $this->stripeApi->getConnectedAccounts();
        $availableWebhookEndpoints  = $this->stripeApi->getWebhookEndpoints();
        $availableCoupons           = $this->stripeApi->getCoupons();
        
        return $this->render( '@VSPayment/Pages/GatewayConfig/Stripe/index.html.twig', [
            'availablePlans'                => $availablePlans,
            'availableProducts'             => $availableProducts,
            'availablePrices'               => $availablePrices,
            'availableCustomers'            => $availableCustomers,
            'availablePaymentMethods'       => $availablePaymentMethods,
            'availableSubscriptions'        => $availableSubscriptions,
            'availableConnectedAccounts'    => $availableConnectedAccounts,
            'availableWebhookEndpoints'     => $availableWebhookEndpoints,
            'availableCoupons'              => $availableCoupons,
        ]);
    }
}