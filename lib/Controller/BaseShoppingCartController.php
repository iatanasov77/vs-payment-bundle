<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\UsersBundle\Security\SecurityBridge;

class BaseShoppingCartController extends AbstractController
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
    
    protected function createCard( Request $request )
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        $em    = $this->doctrine->getManager();
        $card  = $this->ordersFactory->createNew();
        
        $card->setUser( $this->securityBridge->getUser() );
        $card->setSessionId( $session->getId() );
        
        $em->persist( $card );
        $em->flush();
        
        $request->getSession()->set( 'vs_payment_basket_id', $card->getId() );
        return $card;
    }
}