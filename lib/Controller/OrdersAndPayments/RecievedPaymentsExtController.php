<?php namespace Vankosoft\PaymentBundle\Controller\OrdersAndPayments;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Form\PaymentFilterForm;

class RecievedPaymentsExtController extends AbstractController
{
    /** @var Environment */
    private $templatingEngine;
    
    /** @var RepositoryInterface */
    private $repositoryPayments;
    
    public function __construct(
        Environment $templatingEngine,
        RepositoryInterface $repositoryPayments
    ) {
        $this->templatingEngine     = $templatingEngine;
        $this->repositoryPayments   = $repositoryPayments;
    }
    
    public function handleSearchForm( Request $request ): Response
    {
        $form   = $this->createForm( PaymentFilterForm::class, null, ['method' => 'POST'] );
        $form->handleRequest( $request );
        
        if ( $form->isSubmitted() ) {
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'data'      => $this->searchPayments( $form->getData() ),
            ]);
        }
        
        return new JsonResponse([
            'status'    => Status::STATUS_ERROR,
            'message'   => 'GET Method Not Supported !!!',
        ]);
    }
    
    private function searchPayments( $filter )
    {
        $queryBuilder       = $this->repositoryPayments->filterPayments( $filter );
        
        if (
            ( \array_key_exists( 'filterByGatewayFactory', $filter ) && $filter['filterByGatewayFactory'] )
        ) {
            $results    = $this->searchInRelations( $queryBuilder, $filter );
            
            $resources      = new Pagerfanta( new ArrayAdapter( $results ) );
            $items          = $results;
        } else {
            $resources      = new Pagerfanta( new QueryAdapter( $queryBuilder ) );
            //$items          = $resources;
            $items          = $queryBuilder->getQuery()->getResult();
        }
        
        //$resources->setMaxPerPage( 5 );
        return $this->templatingEngine->render( '@VSPayment/Pages/RecievedPayments/payments_table.html.twig', [
            'resources' => $resources,
            'items'     => $items
        ]);
    }
    
    private function searchInRelations( $queryBuilder, $filter ): array
    {
        $results    = [];
        
        foreach ( $queryBuilder->getQuery()->getResult() as $payment ) {
            $inResults  = false;
            
            if ( \array_key_exists( 'filterByGatewayFactory', $filter ) && $filter['filterByGatewayFactory'] ) {
                $inResults  = false;
                
                if ( $payment->getFactoryName() == $filter['ilterByGatewayFactory'] ) {
                    $inResults  = true;
                }
            }
            
            if ( $inResults ) {
                $results[]  = $payment;
            }
        }
        
        return $results;
    }
}