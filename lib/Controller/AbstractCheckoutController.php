<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;

use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\PaymentBundle\EventSubscriber\Event\SubscriptionsPaymentDoneEvent;

abstract class AbstractCheckoutController extends AbstractController
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var Payum */
    protected $payum;
    
    /** @vvar OrderFactory */
    protected $orderFactory;
    
    /** @var string */
    protected $paymentClass;
    
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
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        Payum $payum,
        OrderFactory $orderFactory,
        string $paymentClass,
        ?string $routeRedirectOnShoppingCartDone,
        ?string $routeRedirectOnPricingPlanDone
    ) {
        $this->tokenStorage                         = $tokenStorage;
        $this->eventDispatcher                      = $eventDispatcher;
        $this->translator                           = $translator;
        $this->doctrine                             = $doctrine;
        $this->payum                                = $payum;
        $this->orderFactory                         = $orderFactory;
        
        $this->paymentClass                         = $paymentClass;
        $this->routeRedirectOnShoppingCartDone      = $routeRedirectOnShoppingCartDone;
        $this->routeRedirectOnPricingPlanDone       = $routeRedirectOnPricingPlanDone;
    }
    
    abstract public function prepareAction( Request $request ): Response;
    
    public function doneAction( Request $request ): Response
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $this->payum->getHttpRequestVerifier()->invalidate( $token );  // you can invalidate the token. The url could not be requested any more.
        
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
    
    protected function paymentSuccess( Request $request, $paymentStatus ): Response
    {
        $payment        = $this->_setPaymentSuccess( $paymentStatus );
        $subscriptions  = $payment->getOrder()->getSubscriptions();
        $hasPricingPlan = ! empty( $subscriptions );
        $response       = null;
        $request->getSession()->remove( OrderFactory::SESSION_BASKET_KEY );
        
        if ( $hasPricingPlan ) {
            $response   = $this->_setSubscriptionsPaymentDone( $request, $subscriptions );
        }
        
        if ( ! $hasPricingPlan && $this->routeRedirectOnShoppingCartDone ) {
            $response   = $this->_setShoppingCartPaymentDone( $request );
        }
        
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
        
        throw new HttpException( 400, $this->getErrorMessage( $paymentStatus->getModel() ) );
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
    
    protected function _setSubscriptionsPaymentDone( Request $request, $subscriptions ): ?Response
    {
        if ( $this instanceof AbstractCheckoutOfflineController ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_payment_waiting', [], 'VSPaymentBundle' );
        } else {
            $this->eventDispatcher->dispatch(
                new SubscriptionsPaymentDoneEvent( $subscriptions ),
                SubscriptionsPaymentDoneEvent::NAME
            );
            
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
    
    protected function getErrorMessage( $details )
    {
        return 'STRIPE ERROR: ' . $details['error']['message'];
    }
    
    protected function debugObject( $object )
    {
        $serializer         = \JMS\Serializer\SerializerBuilder::create()->build();
        $serializedObject   = $serializer->serialize( $object, 'json' );
        
        \file_put_contents( '/tmp/DebugPaymentBundle', $serializedObject );
    }
}
