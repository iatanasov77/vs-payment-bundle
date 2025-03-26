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
    private $exchangeRateServiceRepository;
    
    /** @var SwapBuilder */
    private $swapBuilder;
    
    public function __construct( RepositoryInterface $exchangeRateServiceRepository )
    {
        $this->exchangeRateServiceRepository    = $exchangeRateServiceRepository;
        
        $this->swapBuilder = new SwapBuilder();
    }
    
    public function getExchangeRate( Request $request ): Response
    {
        /** @var Swap */
        $swap = $this->swapBuilder->add( 'european_central_bank' )->build();
        //$rate = $swap->latest( 'EUR/BGN' );
        
        //$swap = $this->swapBuilder->add( 'apilayer_exchange_rates_data', ['api_key' => '35d60a160b1c2e7c4565d6cc976f7871'] )->build();
        $rate = $swap->latest( 'EUR/USD' );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
            'data'      => $rate->getValue(),
        ]);
    }
}
