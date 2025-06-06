<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\ManagerRegistry;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;

use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\CatalogBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;
use Vankosoft\PaymentBundle\Component\Exception\CheckoutException;
use Vankosoft\PaymentBundle\Component\Catalog\CatalogBridgeInterface;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CancelSubscription;

abstract class AbstractCheckoutController extends AbstractController
{
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var Payum */
    protected $payum;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Payment */
    protected $vsPayment;
    
    /** @vvar OrderFactory */
    protected $orderFactory;
    
    /** @var CatalogBridgeInterface */
    protected $subscriptionsBridge;
    
    /** @var string */
    protected $paymentClass;
    
    /** @var bool */
    protected $throwExceptionOnPaymentDone;
    
    /**
     * If is set, Done Action will redirect to this url
     *
     * @var string | null
     */
    protected $routeRedirectOnShoppingCartDone;
    
    /**
     * If is set, Done Action will redirect to this url
     * 
     * @var string | null
     */
    protected $routeRedirectOnPricingPlanDone;
    
    /**
     * If is set, Subscription Done Action will redirect to this url
     *
     * @var string | null
     */
    protected $routeRedirectOnSubscriptionActionDone;
    
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        Payum $payum,
        SecurityBridge $securityBridge,
        Payment $vsPayment,
        OrderFactory $orderFactory,
        CatalogBridgeInterface $subscriptionsBridge,
        string $paymentClass,
        bool $throwExceptionOnPaymentDone,
        ?string $routeRedirectOnShoppingCartDone,
        ?string $routeRedirectOnPricingPlanDone,
        ?string $routeRedirectOnSubscriptionActionDone
    ) {
        $this->eventDispatcher                          = $eventDispatcher;
        $this->translator                               = $translator;
        $this->doctrine                                 = $doctrine;
        $this->payum                                    = $payum;
        $this->securityBridge                           = $securityBridge;
        $this->vsPayment                                = $vsPayment;
        $this->orderFactory                             = $orderFactory;
        $this->subscriptionsBridge                      = $subscriptionsBridge;
        
        $this->paymentClass                             = $paymentClass;
        $this->throwExceptionOnPaymentDone              = $throwExceptionOnPaymentDone;
        $this->routeRedirectOnShoppingCartDone          = $routeRedirectOnShoppingCartDone;
        $this->routeRedirectOnPricingPlanDone           = $routeRedirectOnPricingPlanDone;
        $this->routeRedirectOnSubscriptionActionDone    = $routeRedirectOnSubscriptionActionDone;
    }
    
    abstract public function prepareAction( Request $request ): Response;
    
    public function doneAction( Request $request ): Response
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        
        // you can invalidate the token. The url could not be requested any more.
        $this->payum->getHttpRequestVerifier()->invalidate( $token );
        
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        $gateway->execute( $paymentStatus = new GetHumanStatus( $token ) );
        
        // using shortcut
        if ( $paymentStatus->isCaptured() || $paymentStatus->isAuthorized() || $paymentStatus->isPending() ) {
            // success
            return $this->paymentSuccess( $request, $paymentStatus );
        }
        
        // using shortcut
        if ( $paymentStatus->isFailed() || $paymentStatus->isCanceled() ) {
            // failure
            return $this->paymentFailed( $request, $paymentStatus );
        }
    }
    
    protected function jsonResponse( string $status, string $redirectUrl ): JsonResponse
    {
        return new JsonResponse([
            'status'    => $status,
            'data'      => [
                'redirecrUrl'   => $redirectUrl,
            ]
        ]);
    }
    
    protected function setUserPaymentDetails( PaymentInterface $payment ): void
    {
        $user   = $this->securityBridge->getUser();
        if ( $user ) {
            $userPaymentDetails = $user->getPaymentDetails();
            $paymentDetails     = $payment->getDetails();
            $factory            = $payment->getFactoryName();
            
            switch ( $factory ) {
                case 'stripe_checkout':
                case 'stripe_js':
                    if (
                        isset ( $userPaymentDetails[StripeApi::CUSTOMER_ATTRIBUTE_KEY] ) &&
                        $userPaymentDetails[StripeApi::CUSTOMER_ATTRIBUTE_KEY] == $paymentDetails['customer']
                    ) {
                        return;
                    }
                    
                    $userPaymentDetails[StripeApi::CUSTOMER_ATTRIBUTE_KEY] = $paymentDetails['customer'];
                    $user->setPaymentDetails( $userPaymentDetails );
                    
                    $em = $this->doctrine->getManager();
                    $em->persist( $user );
                    $em->flush();
                    
                    break;
            }
        }
    }
    
    protected function paymentSuccess( Request $request, $paymentStatus ): Response
    {
        $payment        = $this->_setPaymentSuccess( $paymentStatus );
        $subscriptions  = $payment->getOrder()->getSubscriptions();
        $hasPricingPlan = ! empty( $subscriptions );
        $response       = null;
        $request->getSession()->remove( OrderFactory::SESSION_BASKET_KEY );
        
        if ( $hasPricingPlan ) {
            $response   = $this->_setSubscriptionsPaymentDone( $request, $subscriptions, $payment );
        }
        
        if ( ! $hasPricingPlan && $this->routeRedirectOnShoppingCartDone ) {
            $response   = $this->_setShoppingCartPaymentDone( $request );
        }
        
        $this->setUserPaymentDetails( $payment );
        
        if ( $response ) {
            return $response;
        } else {
            return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
                'shoppingCart'                      => $this->orderFactory->getShoppingCart(),
                'paymentStatus'                     => $paymentStatus,
                'routeRedirectOnShoppingCartDone'   => $this->routeRedirectOnShoppingCartDone,
                'routeRedirectOnPricingPlanDone'    => $this->routeRedirectOnPricingPlanDone,
                'hasPricingPlan'                    => $hasPricingPlan,
            ]);
        }
    }
    
    protected function paymentFailed( Request $request, $paymentStatus ): Response
    {
        $storage    = $this->payum->getStorage( $this->paymentClass );
        $payment    = $paymentStatus->getFirstModel();
        
        $payment->getOrder()->setStatus( Order::STATUS_FAILED_ORDER );
        $storage->update( $payment );
        $request->getSession()->remove( OrderFactory::SESSION_BASKET_KEY );
        
        $this->setUserPaymentDetails( $payment );
        
        if ( $this->throwExceptionOnPaymentDone ) {
            throw new CheckoutException(
                $payment->getOrder()->getPaymentMethod()->getGateway()->getFactoryName(),
                $paymentStatus->getModel()
            );
        }
        
        return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
            'shoppingCart'                      => $this->orderFactory->getShoppingCart(),
            'paymentStatus'                     => $paymentStatus,
            'routeRedirectOnShoppingCartDone'   => $this->routeRedirectOnShoppingCartDone,
            'routeRedirectOnPricingPlanDone'    => $this->routeRedirectOnPricingPlanDone,
            'hasPricingPlan'                    => false,
        ]);
    }
    
    protected function _setPaymentSuccess( $paymentStatus ): PaymentInterface
    {
        $storage    = $this->payum->getStorage( $this->paymentClass );
        $payment    = $paymentStatus->getFirstModel();
        //$this->debugObject( $payment );
        
        if ( $this instanceof AbstractCheckoutOfflineController ) {
            $payment->getOrder()->setStatus( Order::STATUS_PENDING_ORDER );
        } else {
            $payment->getOrder()->setStatus( Order::STATUS_PAID_ORDER );
        }
        
        $storage->update( $payment );
        
        return  $payment;
    }
    
    protected function _setSubscriptionsPaymentDone( Request $request, $subscriptions, $payment ): ?Response
    {
        if ( ! $this->vsPayment->triggerSubscriptionsPaymentDone( $payment ) ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_payment_waiting', [], 'VSPaymentBundle' );
        } else {
            $this->subscriptionsBridge->triggerSubscriptionsPaymentDone( $subscriptions, $payment );
            
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_payment_success', [], 'VSPaymentBundle' );
        }
        
        if ( $this->routeRedirectOnPricingPlanDone ) {
            $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
            
            return $this->redirectToRoute( $this->routeRedirectOnPricingPlanDone );
        }
        
        return null;
    }
    
    protected function _setShoppingCartPaymentDone( Request $request ): ?Response
    {
        if ( $this instanceof AbstractCheckoutOfflineController ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.shopping_cart_payment_waiting', [], 'VSPaymentBundle' );
        } else {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.shopping_cart_payment_success', [], 'VSPaymentBundle' );
        }
        
        if ( $this->routeRedirectOnShoppingCartDone ) {
            $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
            
            return $this->redirectToRoute( $this->routeRedirectOnShoppingCartDone );
        }
        
        return null;
    }
    
    protected function debugObject( $object )
    {
        $serializer         = \JMS\Serializer\SerializerBuilder::create()->build();
        $serializedObject   = $serializer->serialize( $object, 'json' );
        
        \file_put_contents( '/tmp/DebugPaymentBundle', $serializedObject );
    }
}
