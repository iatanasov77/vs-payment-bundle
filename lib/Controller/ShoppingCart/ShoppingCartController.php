<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Vankosoft\PaymentBundle\Controller\BaseShoppingCartController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\UsersBundle\Security\SecurityBridge;

class ShoppingCartController extends BaseShoppingCartController
{
    public function __construct( ManagerRegistry $doctrine, SecurityBridge $securityBridge, EntityRepository $ordersRepository )
    {
        parent::__construct( $doctrine, $securityBridge, $ordersRepository );
    }
    
    public function index( Request $request ): Response
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        //$shoppingCart   = $this->ordersRepository->getShoppingCart( $this->securityBridge->getUser(), $session->getId() );
        
        $cardId         = $session->get( 'vs_payment_basket_id' );
        $shoppingCart   = $cardId ? $this->ordersRepository->find( $cardId ) : $this->createCard( $request );
        
        return $this->render( '@VSPayment/Pages/ShoppingCart/index.html.twig', [
            'shoppingCart'  => $shoppingCart,
            'items'         => $shoppingCart ? $shoppingCart->getItems() : [],
        ]);
    }
}