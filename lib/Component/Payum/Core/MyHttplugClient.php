<?php  namespace Vankosoft\PaymentBundle\Component\Payum\Core;

use Payum\Core\HttpClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\HttplugClient;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;

/**
 * This is a HttpClient that support Httplug.
 * This is an adapter class that make sure we can use Httplug without breaking
 */
class MyHttplugClient implements HttpClientInterface
{
    /**
     * @var SymfonyHttpClientInterface
     */
    private $client;

    public function __construct()
    {
        // Dont Verify SSL certificate
        // These should moved in bundle config and disable host verification for DEV Environement Only.
        $this->client   = new HttplugClient( HttpClient::create([
            "verify_peer"   =>false,
            "verify_host"   =>false
        ]));
    }

    /**
     * {@inheritDoc}
     */
    public function send( RequestInterface $request )
    {
        return $this->client->sendRequest($request);
    }
}
