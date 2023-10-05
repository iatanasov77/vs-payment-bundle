<?php namespace Vankosoft\PaymentBundle\Controller\PricingPlans;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class PricingPlanExtController extends AbstractController
{
    /** @var RepositoryInterface */
    protected $pricingPlansRepository;
    
    /** @var RepositoryInterface */
    protected $paidServicesRepository;
    
    public function __construct(
        RepositoryInterface $pricingPlanRepository,
        RepositoryInterface $payedServiceRepository
    ) {
        $this->pricingPlansRepository   = $pricingPlanRepository;
        $this->paidServicesRepository   = $payedServiceRepository;
    }
    
    public function getPaidServicesJson( $id, Request $request ): Response
    {
        $selectedValues = $this->pricingPlansRepository->find( $id )->getPaidServices();
        
        $data           = [];
        $this->buildEasyuiCombotreeData(
            $this->paidServicesRepository->findAll(),
            $data,
            $selectedValues->getKeys(),
            [],
            false
        );
        
        return new JsonResponse( $data );
    }
    
    protected function buildEasyuiCombotreeData( $tree, &$data, array $selectedValues, array $leafs, $notLeafs )
    {
        $key    = 0;
        foreach( $tree as $node ) {
            $data[$key]   = [
                'id'        => $node->getId(),
                'text'      => $node->getTitle(),
                'children'  => [],
                'disabled'  => ! $notLeafs
            ];
            if ( \in_array( $node->getId(), $selectedValues ) ) {
                $data[$key]['checked'] = true;
            }
            
            if ( \array_key_exists( $node->getId(), $leafs ) ) {
                $this->buildEasyuiCombotreeData( $leafs[$node->getId()], $data[$key]['children'], $selectedValues, $leafs, false );
            }
            
            // Buld Child Categories After Leafs because Leafs override children keys
            if ( $node->getSubscriptionPeriods()->count() ) {
                $this->buildEasyuiCombotreeData( $node->getSubscriptionPeriods(), $data[$key]['children'], $selectedValues, $leafs, true );
            }
            
            $key++;
        }
    }
}