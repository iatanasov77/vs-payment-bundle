<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\UsersBundle\Security\SecurityBridge;

class BaseShoppingCartController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Factory $ordersFactory,
        EntityRepository $ordersRepository
    ) {
        $this->doctrine         = $doctrine;
        $this->securityBridge   = $securityBridge;
        $this->ordersFactory    = $ordersFactory;
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