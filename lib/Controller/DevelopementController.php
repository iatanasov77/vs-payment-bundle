<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Some Actions For Clearing Developement/Testing Data
 */
class DevelopementController extends AbstractController
{
    /** @var RepositoryInterface */
    private $ordersRepository;
    
    /** @var RepositoryInterface */
    private $orderItemsRepository;
    
    /** @var RepositoryInterface */
    private $paymentsRepository;
    
    /** @var RepositoryInterface */
    private $paymentTokensRepository;
    
    /** @var RepositoryInterface */
    private $pricingPlanSubscriptionsRepository;
    
    public function __construct(
        RepositoryInterface $ordersRepository,
        RepositoryInterface $orderItemsRepository,
        RepositoryInterface $paymentsRepository,
        RepositoryInterface $paymentTokensRepository,
        RepositoryInterface $pricingPlanSubscriptionsRepository
    ) {
        $this->ordersRepository                     = $ordersRepository;
        $this->orderItemsRepository                 = $orderItemsRepository;
        $this->paymentsRepository                   = $paymentsRepository;
        $this->paymentTokensRepository              = $paymentTokensRepository;
        $this->pricingPlanSubscriptionsRepository   = $pricingPlanSubscriptionsRepository;
    }
    
    public function deleteAllOrdersAction( Request $request ): Response
    {
        $deletedRecords = $this->orderItemsRepository->createQueryBuilder( 'oi' )
                                ->delete()->getQuery()->getSingleScalarResult() ?? 0;
        $deletedRecords += $this->ordersRepository->createQueryBuilder( 'o' )
                                ->delete()->getQuery()->getSingleScalarResult() ?? 0;
        $deletedRecords += $this->paymentsRepository->createQueryBuilder( 'p' )
                                ->delete()->getQuery()->getSingleScalarResult() ?? 0;
        $deletedRecords += $this->paymentTokensRepository->createQueryBuilder( 'pt' )
                                ->delete()->getQuery()->getSingleScalarResult() ?? 0;
        $deletedRecords += $this->pricingPlanSubscriptionsRepository->createQueryBuilder( 'pps' )
                                ->delete()->getQuery()->getSingleScalarResult() ?? 0;
        
        return $this->redirectToRoute( 'app_home' );
    }
}