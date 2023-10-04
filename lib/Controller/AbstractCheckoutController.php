<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;

use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

abstract class AbstractCheckoutController extends AbstractController
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var Payum */
    protected $payum;
    
    /** @var string */
    protected $paymentClass;
    
    /** @var RepositoryInterface */
    protected $subscriptionRepository;
    
    /** @var Factory */
    protected $subscriptionFactory;
    
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
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        RepositoryInterface $ordersRepository,
        Factory $ordersFactory,
        Payum $payum,
        string $paymentClass,
        RepositoryInterface $subscriptionRepository,
        Factory $subscriptionFactory,
        ?string $routeRedirectOnShoppingCartDone,
        ?string $routeRedirectOnPricingPlanDone
    ) {
        $this->tokenStorage                     = $tokenStorage;
        $this->translator                       = $translator;
        $this->doctrine                         = $doctrine;
        $this->ordersRepository                 = $ordersRepository;
        $this->ordersFactory                    = $ordersFactory;
        $this->payum                            = $payum;
        $this->paymentClass                     = $paymentClass;
        $this->subscriptionRepository           = $subscriptionRepository;
        $this->subscriptionFactory              = $subscriptionFactory;
        $this->routeRedirectOnShoppingCartDone  = $routeRedirectOnShoppingCartDone;
        $this->routeRedirectOnPricingPlanDone   = $routeRedirectOnPricingPlanDone;
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
        $storage    = $this->payum->getStorage( $this->paymentClass );
        $payment    = $paymentStatus->getFirstModel();
        //$this->debugObject( $payment );
        
        $payment->getOrder()->setStatus( Order::STATUS_PAID_ORDER );
        $storage->update( $payment );
        $request->getSession()->remove( 'vs_payment_basket_id' );
        
        $hasPricingPlan = $this->setSubscription( $payment->getOrder() );
        if ( $hasPricingPlan && $this->routeRedirectOnPricingPlanDone ) {
            $flashMessage   = $this->translator->trans( 'pricing_plan_payment_success', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
            
            return $this->redirectToRoute( $this->routeRedirectOnPricingPlanDone );
        }
        
        if ( ! $hasPricingPlan && $this->routeRedirectOnShoppingCartDone ) {
            $flashMessage   = $this->translator->trans( 'shopping_cart_payment_success', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
            
            return $this->redirectToRoute( $this->routeRedirectOnShoppingCartDone );
        }
        
        return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
            'shoppingCart'                      => $this->getShoppingCart( $request ),
            'paymentStatus'                     => $paymentStatus,
            'routeRedirectOnShoppingCartDone'   => $this->routeRedirectOnShoppingCartDone,
            'routeRedirectOnPricingPlanDone'    => $this->routeRedirectOnPricingPlanDone,
            'hasPricingPlan'                    => $hasPricingPlan,
        ]);
    }
    
    protected function paymentFailed( Request $request, $paymentStatus ): Response
    {
        $storage    = $this->payum->getStorage( $this->paymentClass );
        $payment    = $paymentStatus->getFirstModel();
        
        $payment->getOrder()->setStatus( Order::STATUS_FAILED_ORDER );
        $storage->update( $payment );
        $request->getSession()->remove( 'vs_payment_basket_id' );
        
        throw new HttpException( 400, $this->getErrorMessage( $paymentStatus->getModel() ) );
        return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
            'shoppingCart'                      => $this->getShoppingCart( $request ),
            'paymentStatus'                     => $paymentStatus,
            'routeRedirectOnShoppingCartDone'   => $this->routeRedirectOnShoppingCartDone,
            'routeRedirectOnPricingPlanDone'    => $this->routeRedirectOnPricingPlanDone,
            'hasPricingPlan'                    => false,
        ]);
    }
    
    protected function getShoppingCart( Request $request ): OrderInterface
    {
        $em      = $this->doctrine->getManager();
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        $cartId         = $session->get( 'vs_payment_basket_id' );
        $shoppingCart   = $cartId ? $this->ordersRepository->find( $cartId ) : null;
        if ( ! $shoppingCart ) {
            $shoppingCart   = $this->ordersFactory->createNew();
            $user           = $this->tokenStorage->getToken()->getUser();
            
            $shoppingCart->setUser( $user );
            $shoppingCart->setSessionId( $session->getId() );
            
            $em->persist( $shoppingCart );
            $em->flush();
        }
        
        return $shoppingCart;
    }
    
    protected function getErrorMessage( $details )
    {
        return 'STRIPE ERROR: ' . $details['error']['message'];
    }
    
    protected function setSubscription( OrderInterface $order ): bool
    {
        $em             = $this->doctrine->getManager();
        $hasPricingPlan = false;
        
        foreach( $order->getItems() as $item ) {
            $payableObject  = $item->getObject();
            if ( ! ( $payableObject instanceof PricingPlanInterface ) ) {
                continue;
            }
            
            $subscription   = $this->subscriptionFactory->createNew();
            
            $hasPricingPlan = true;
            $user           = $this->tokenStorage->getToken()->getUser();
            
            $subscription->setUser( $user );
            $subscription->setPayedService( $payableObject->getPaidServicePeriod() );
            
            $subscription->setSubscriptionCode( $payableObject->getSubscriptionCode() );
            $subscription->setSubscriptionPriority( $payableObject->getSubscriptionPriority() );
            
            $subscription->setDate( new \DateTime() );
            
            $em->persist( $subscription );
            $em->flush();
            
            $user->addPaidSubscription( $subscription );
            $em->persist( $user );
            $em->flush();
        }
        
        return $hasPricingPlan;
    }
    
    protected function debugObject( $object )
    {
        $serializer         = \JMS\Serializer\SerializerBuilder::create()->build();
        $serializedObject   = $serializer->serialize( $object, 'json' );
        
        \file_put_contents( '/tmp/DebugPaymentBundle', $serializedObject );
    }
}
