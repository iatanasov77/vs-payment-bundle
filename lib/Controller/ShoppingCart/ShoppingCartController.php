<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\UsersBundle\Security\SecurityBridge;

class ShoppingCartController extends AbstractController
{
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    public function __construct( SecurityBridge $securityBridge, EntityRepository $ordersRepository )
    {
        $this->securityBridge   = $securityBridge;
        $this->ordersRepository = $ordersRepository;
    }
    
    public function index( Request $request ): Response
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        //$shoppingCart   = $this->ordersRepository->getShoppingCart( $this->securityBridge->getUser(), $session->getId() );
        
        $cardId         = $session->get( 'vs_payment_basket_id' );
        $shoppingCart   = $this->ordersRepository->find( $cardId );
        
        return $this->render( '@VSPayment/Pages/ShoppingCart/index.html.twig', [
            'shoppingCart'  => $shoppingCart,
            'items'         => $shoppingCart ? $shoppingCart->getItems() : [],
        ]);
    }
}