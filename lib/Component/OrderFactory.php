<?php namespace Vankosoft\PaymentBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\UsersBundle\Model\UserInterface;

class OrderFactory
{
    /** @var UserInterface|null */
    private $user;
    
    /** @var Request|null */
    private $request;
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RepositoryInterface */
    private $ordersRepository;
    
    /** @var Factory */
    private $ordersFactory;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        RequestStack $requestStack,
        ManagerRegistry $doctrine,
        RepositoryInterface $ordersRepository,
        Factory $ordersFactory
    ) {
        $this->doctrine         = $doctrine;
        $this->ordersRepository = $ordersRepository;
        $this->ordersFactory    = $ordersFactory;
        
        $this->request          = $requestStack->getCurrentRequest();
        
        $token                  = $tokenStorage->getToken();
        if ( $token ) {
            $this->user         = $token->getUser();
        }
    }
    
    public function getShoppingCart(): OrderInterface
    {
        $em      = $this->doctrine->getManager();
        $session = $this->request->getSession();
        $session->start();  // Ensure Session is Started
        
        $cartId         = $session->get( 'vs_payment_basket_id' );
        $shoppingCart   = $cartId ? $this->ordersRepository->find( $cartId ) : null;
        if ( ! $shoppingCart ) {
            $shoppingCart   = $this->ordersFactory->createNew();
            
            $shoppingCart->setUser( $this->user );
            $shoppingCart->setSessionId( $session->getId() );
            
            $em->persist( $shoppingCart );
            $em->flush();
            
            $this->request->getSession()->set( 'vs_payment_basket_id', $shoppingCart->getId() );
        }
        
        return $shoppingCart;
    }
}