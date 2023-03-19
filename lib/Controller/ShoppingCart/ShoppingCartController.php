<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class ShoppingCartController extends AbstractController
{
    /** @var EntityRepository */
    private $ordersRepository;
    
    public function __construct( EntityRepository $ordersRepository )
    {
        $this->ordersRepository = $ordersRepository;
    }
    
    public function index( Request $request ): Response
    {
        return $this->render( '@VSPayment/Pages/ShoppingCart/index.html.twig', [
            'items' => $this->ordersRepository->findAll(),
        ]);
    }
}