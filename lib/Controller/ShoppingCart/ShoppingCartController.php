<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface;

class ShoppingCartController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var EntityRepository */
    protected $productsRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Factory $ordersFactory,
        EntityRepository $ordersRepository,
        Factory $orderItemsFactory,
        EntityRepository $productsRepository
    ) {
        $this->doctrine             = $doctrine;
        $this->securityBridge       = $securityBridge;
        $this->ordersFactory        = $ordersFactory;
        $this->ordersRepository     = $ordersRepository;
        $this->orderItemsFactory    = $orderItemsFactory;
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
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $payableObject  = $this->productsRepository->find( $payableObjectId );
        
        $orderItem->setProduct( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $cart->addItem( $orderItem );
        $em->persist( $cart );
        $em->flush();
        
        return $payableObject;
    }
}