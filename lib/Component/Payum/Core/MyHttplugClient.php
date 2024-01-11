<?php  namespace Vankosoft\PaymentBundle\Component\Payum\Core;

use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\MyHttplugClient;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;

/**
 * This is a HttpClient that support Httplug. This is an adapter class that make sure we can use Httplug without breaking
 * backward compatibility. At 2.0 we will be using Http\Client\HttpClient.
 *
 * @deprecated This will be removed in 2.0. Consider using Http\Client\HttpClient.
 */
class MyHttplugClient extends MyHttplugClient implements HttpClientInterface
{
    /**
     * @var SymfonyHttpClientInterface
     */
    private $client;

    /**
     * @param HttpClient $client
     */
    //public function __construct( ClientInterface $client )
    public function __construct( SymfonyHttpClientInterface $client )
    {
        //$this->client = $client;
        parent::__construct( $client );
    }

    /**
     * {@inheritDoc}
     */
    public function send( RequestInterface $request )
    {
        return $this->client->sendRequest($request);
    }
}
