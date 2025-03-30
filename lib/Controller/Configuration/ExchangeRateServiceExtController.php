<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Swap\Builder as SwapBuilder;
use Swap\Swap;
use Vankosoft\ApplicationBundle\Component\Status;

class ExchangeRateServiceExtController extends AbstractController
{
    /** @var RepositoryInterface */
    private $exchangeRateRepository;
    
    /** @var RepositoryInterface */
    private $exchangeRateServiceRepository;
    
    /** @var SwapBuilder */
    private $swapBuilder;
    
    public function __construct(
        RepositoryInterface $exchangeRateRepository,
        RepositoryInterface $exchangeRateServiceRepository
    ) {
        $this->exchangeRateRepository           = $exchangeRateRepository;
        $this->exchangeRateServiceRepository    = $exchangeRateServiceRepository;
        
        $this->swapBuilder = new SwapBuilder();
    }
    
    public function getExchangeRate( $serviceId, $exchangeRateId, Request $request ): Response
    {
        // Ex. european_central_bank
        $service    = $this->exchangeRateServiceRepository->findOneBy(['serviceId' => $serviceId]);
        if ( ! $service ) {
            throw new \Exception( 'The Exchange Rate Service Not Found !!!' );
        }
        
        $serviceOptions = [];
        foreach ( $service->getOptions() as $option ) {
            if ( $option['key'] && $option['value'] ) {
                $serviceOptions[$option['key']] = $option['value'];
            }
        }
        
        /** @var Swap */
        $swap           = $this->swapBuilder->add( $service->getServiceId(), $serviceOptions )->build();
        $exchangeRate   = $this->exchangeRateRepository->find( $exchangeRateId );
        
        $rate = $swap->latest( \sprintf( '%s/%s',
            $exchangeRate->getSourceCurrency()->getCode(),
            $exchangeRate->getTargetCurrency()->getCode()
        ));
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $rate->getValue(),
        ]);
    }
}
