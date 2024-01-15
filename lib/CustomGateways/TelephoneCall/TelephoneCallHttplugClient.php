<?php  namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttplugClient;

/**
 * This is a HttpClient that support Httplug.
 * This is an adapter class that make sure we can use Httplug without breaking
 */
class TelephoneCallHttplugClient implements HttpClientInterface
{
    /**
     * @var HttplugClien
     */
    private $client;

    public function __construct( array $httpClientOptions = [] )
    {
        $this->client   = new HttplugClient( HttpClient::create( $httpClientOptions ) );
    }

    /**
     * {@inheritDoc}
     */
    public function send( RequestInterface $request )
    {
        return $this->client->sendRequest($request);
    }
}
