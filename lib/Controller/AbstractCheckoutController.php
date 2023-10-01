<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\CreditCard;

use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Exception\ShoppingCartException;

abstract class AbstractCheckoutController extends AbstractController
{
    /** @var TokenStorageInterface */
    protected $tokenStorage;
    
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    /** @var Payum */
    protected $payum;
    
    /** @var string */
    protected $paymentClass;
    
    /** @var EntityRepository */
    protected $subscriptionRepository;
    
    /** @var Factory */
    protected $subscriptionFactory;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ManagerRegistry $doctrine,
        EntityRepository $ordersRepository,
        Payum $payum,
        string $paymentClass,
        EntityRepository $subscriptionRepository,
        Factory $subscriptionFactory
    ) {
        $this->tokenStorage             = $tokenStorage;
        $this->doctrine                 = $doctrine;
        $this->ordersRepository         = $ordersRepository;
        $this->payum                    = $payum;
        $this->paymentClass             = $paymentClass;
        $this->subscriptionRepository   = $subscriptionRepository;
        $this->subscriptionFactory      = $subscriptionFactory;
    }
    
    abstract public function prepareAction( Request $request ): Response;
    
    public function doneAction( Request $request ): Response
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $this->payum->getHttpRequestVerifier()->invalidate( $token );  // you can invalidate the token. The url could not be requested any more.
        
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        $gateway->execute( $status = new GetHumanStatus( $token ) );
        
        $storage    = $this->payum->getStorage( $this->paymentClass );
        $payment    = $status->getFirstModel();
        
        // using shortcut
        if ( $status->isCaptured() || $status->isAuthorized() || $status->isPending() ) {
            // success
            $payment->getOrder()->setStatus( Order::STATUS_PAID_ORDER );
            //$this->debugObject( $payment );
            $storage->update( $payment );
            $request->getSession()->remove( 'vs_payment_basket_id' );
            
            $this->setSubscription( $payment->getOrder() );
            
            return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
                'paymentStatus' => $status,
            ]);
        }
        
        // using shortcut
        if ( $status->isFailed() || $status->isCanceled() ) {
            $payment->getOrder()->setStatus( Order::STATUS_FAILED_ORDER );
            $storage->update( $payment );
            $request->getSession()->remove( 'vs_payment_basket_id' );
            
            throw new HttpException( 400, $this->getErrorMessage( $status->getModel() ) );
        }
    }

    protected function getShoppingCart( Request $request )
    {
        $cartId = $request->getSession()->get( 'vs_payment_basket_id' );
        if ( ! $cartId ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in session !!!' );
        }
        $cart   = $this->ordersRepository->find( $cartId );
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in repository !!!' );
        }
        
        return $card;
    }
    
    protected function createCreditCard( $details )
    {
        $card = new CreditCard();
        
        $card->setNumber( $details['number'] );
        $card->setExpireAt( new \DateTime('2018-10-10') );
        $card->setSecurityCode( $details['cvv'] );
        $card->setHolder( $details['holder'] );
        
        return $card;
    }
    
    protected function getErrorMessage( $details )
    {
        return 'STRIPE ERROR: ' . $details['error']['message'];
    }
    
    protected function setSubscription( Order $order )
    {
        $em = $this->doctrine->getManager();
        
        foreach( $order->getItems() as $item ) {
            $subscription   = $this->subscriptionFactory->createNew();
            $payableObject  = $item->getObject();
            
            $subscription->setUser( $this->tokenStorage->getToken()->getUser() );
            $subscription->setPayedService( $payableObject );
            
            $subscription->setSubscriptionCode( $payableObject->getSubscriptionCode() );
            $subscription->setSubscriptionPriority( $payableObject->getSubscriptionPriority() );
            
            $em->persist( $subscription );
            $em->flush();
        }
    }
    
    protected function debugObject( $object )
    {
        $serializer         = \JMS\Serializer\SerializerBuilder::create()->build();
        $serializedObject   = $serializer->serialize( $object, 'json' );
        
        \file_put_contents( '/tmp/DebugPaymentBundle', $serializedObject );
    }
}
