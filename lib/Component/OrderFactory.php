<?php namespace Vankosoft\PaymentBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\ApplicationBundle\Component\Context\ApplicationContextInterface;

class OrderFactory
{
    const SESSION_BASKET_KEY    = 'vs_payment_basket_id';
    
    /** @var Request|null */
    private $request;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var SecurityBridge */
    private $securityBridge;
    
    /** @var RepositoryInterface */
    private $ordersRepository;
    
    /** @var Factory */
    private $ordersFactory;
    
    /** @var ApplicationContextInterface */
    private $applicationContext;
    
    public function __construct(
        RequestStack $requestStack,
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        RepositoryInterface $ordersRepository,
        Factory $ordersFactory,
        ApplicationContextInterface $applicationContext
    ) {
        $this->doctrine             = $doctrine;
        $this->securityBridge       = $securityBridge;
        $this->ordersRepository     = $ordersRepository;
        $this->ordersFactory        = $ordersFactory;
        $this->applicationContext   = $applicationContext;
        
        $this->request              = $requestStack->getCurrentRequest();
    }
    
    public function getShoppingCart(): OrderInterface
    {
        $em      = $this->doctrine->getManager();
        $session = $this->request->getSession();
        $session->start();  // Ensure Session is Started
        
        $cartId         = $session->get( self::SESSION_BASKET_KEY );
        $shoppingCart   = $cartId ?
                            $this->ordersRepository->find( $cartId ) :
                            $this->ordersRepository->getShoppingCartByUser( $this->securityBridge->getUser() );
        
        if ( ! $shoppingCart ) {
            $shoppingCart   = $this->ordersFactory->createNew();
            
            $shoppingCart->setApplication( $this->applicationContext->getApplication() );
            $shoppingCart->setUser( $this->securityBridge->getUser() );
            $shoppingCart->setSessionId( $session->getId() );
            
            $em->persist( $shoppingCart );
            $em->flush();
        }
        $session->set( self::SESSION_BASKET_KEY, $shoppingCart->getId() );
        
        return $shoppingCart;
    }
    
    public function clearShoppingCart(): void
    {
        $em      = $this->doctrine->getManager();
        $session = $this->request->getSession();
        $session->start();  // Ensure Session is Started
        
        $cartId         = $session->get( self::SESSION_BASKET_KEY );
        $shoppingCart   = $cartId ?
                            $this->ordersRepository->find( $cartId ) :
                            $this->ordersRepository->getShoppingCartByUser( $this->securityBridge->getUser() );
        
        if ( $shoppingCart ) {
            foreach ( $shoppingCart->getItems() as $item ) {
                $shoppingCart->removeItem( $item );
            }
            
            $em->persist( $shoppingCart );
            $em->flush();
        }
    }
}