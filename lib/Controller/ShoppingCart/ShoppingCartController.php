<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Component\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface;
use Vankosoft\ApplicationBundle\Component\Status;

class ShoppingCartController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var RepositoryInterface */
    protected $orderItemsRepository;
    
    /** @var RepositoryInterface */
    protected $productsRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Factory $ordersFactory,
        RepositoryInterface $ordersRepository,
        Factory $orderItemsFactory,
        RepositoryInterface $orderItemsRepository,
        RepositoryInterface $productsRepository
    ) {
        $this->doctrine             = $doctrine;
        $this->securityBridge       = $securityBridge;
        $this->ordersFactory        = $ordersFactory;
        $this->ordersRepository     = $ordersRepository;
        $this->orderItemsFactory    = $orderItemsFactory;
        $this->orderItemsRepository = $orderItemsRepository;
        $this->productsRepository   = $productsRepository;
    }
    
    public function index( Request $request ): Response
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        $cartId         = $session->get( 'vs_payment_basket_id' );
        $shoppingCart   = $cartId ? $this->ordersRepository->find( $cartId ) : $this->createCart( $request );
        
        return $this->render( '@VSPayment/Pages/ShoppingCart/index.html.twig', [
            'shoppingCart'  => $shoppingCart,
            'items'         => $shoppingCart ? $shoppingCart->getItems() : [],
        ]);
    }
    
    public function addToCartAction( $payableObjectId, $qty, Request $request ): Response
    {
        $cartId = $request->getSession()->get( 'vs_payment_basket_id' );
        $cart   = $cartId ? $this->ordersRepository->find( $cartId ) : $this->createCart( $request );
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart cannot be created !!!' );
        }
        
        $this->addProductToCart( $payableObjectId, $qty, $cart );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
    
    public function removeFromCartAction( $itemId, Request $request ): Response
    {
        $cartId = $request->getSession()->get( 'vs_payment_basket_id' );
        $cart   = $cartId ? $this->ordersRepository->find( $cartId ) : null;
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart cannot be created !!!' );
        }
        
        $cartItem   = $this->orderItemsRepository->find( $itemId );
        if( $cartItem ) {
            $cart->removeItem( $cartItem );
            
            $em = $this->doctrine->getManager();
            $em->persist( $cart );
            $em->flush();
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
    
    public function updateCartAction( Request $request ): Response
    {
        $cartId = $request->getSession()->get( 'vs_payment_basket_id' );
        $cart   = $cartId ? $this->ordersRepository->find( $cartId ) : null;
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart cannot be created !!!' );
        }
        
        $em             = $this->doctrine->getManager();
        $jsonCartItems  = $request->request->get( 'CartItems' );
        $cartItems      = $cart->getItems();
        foreach( \json_decode( $jsonCartItems ) as $itemId => $itemQty ) {
            $cartItem   = $cartItems->get( $itemId );
            if ( $cartItem ) {
                $cartItem->setQty( $itemQty );
                $em->persist( $cartItem );
            }
        }
        
        $em->persist( $cart );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
    
    protected function createCart( Request $request )
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        $em    = $this->doctrine->getManager();
        $cart  = $this->ordersFactory->createNew();
        
        $cart->setUser( $this->securityBridge->getUser() );
        $cart->setSessionId( $session->getId() );
        
        $em->persist( $cart );
        $em->flush();
        
        $request->getSession()->set( 'vs_payment_basket_id', $cart->getId() );
        return $cart;
    }
    
    protected function addProductToCart( $payableObjectId, $qty, &$cart ): PayableObjectInterface
    {
        $em             = $this->doctrine->getManager();
        
        $orderItem      = null;
        foreach ( $cart->getItems() as $item ) {
            if ( $item->getProduct()->getId() == $payableObjectId ) {
                $orderItem  = $item;
            }
        }
        
        if ( $orderItem ) {
            $orderItem->setQty( $orderItem->getQty() + $qty );
        } else {
            $orderItem      = $this->orderItemsFactory->createNew();
            $payableObject  = $this->productsRepository->find( $payableObjectId );
            
            $orderItem->setProduct( $payableObject );
            $orderItem->setPrice( $payableObject->getPrice() );
            $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
            
            $cart->addItem( $orderItem );
        }
        
        $em->persist( $cart );
        $em->flush();
        
        return $orderItem->getProduct();
    }
}