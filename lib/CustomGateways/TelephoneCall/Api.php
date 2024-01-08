<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Http\Message\MessageFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RuntimeException;
use Payum\Core\HttpClientInterface;

class Api
{
    /**
     * @var HttpClientInterface
     */
    protected $client;
    
    /**
     * @var MessageFactory
     */
    protected $messageFactory;
    
    protected $options = [
        'api_login_endpoint'            => null,
        'api_verify_coupon_endpoint'    => null,
        'username'                      => null,
        'password'                      => null,
        'sandbox'                       => null,
        'useraction'                    => null,
    ];
    
    /**
     * @param HttpClientInterface|null $client
     * @param MessageFactory|null      $messageFactory
     */
    public function __construct( array $options, HttpClientInterface $client, MessageFactory $messageFactory )
    {
        $options    = ArrayObject::ensureArrayObject( $options );
        $options->defaults( $this->options );
        $options->validateNotEmpty([
            'api_login_endpoint',
            'api_verify_coupon_endpoint',
            'username',
            'password',
        ]);
        
        if ( ! is_bool( $options['sandbox'] ) ) {
            throw new InvalidArgumentException( 'The boolean sandbox option must be set.' );
        }
        
        $this->options          = $options;
        $this->client           = $client;
        $this->messageFactory   = $messageFactory;
    }
    
    /**
     * @return array
     */
    protected function doLogin()
    {
        $response   = $this->doRequest(
            $this->options['api_login_endpoint'],
            [
                'username' => $this->options['username'],
                'password' => $this->options['password'],
            ]
        );
        
        return $response;
    }
    
    /**
     * @throws HttpException
     *
     * @return array
     */
    protected function doRequest( string $endpoint, array $fields, $requestMethod = 'POST' )
    {
        $headers    = [
            'Content-Type' => 'application/json',
        ];
        
        $request = $this->messageFactory->createRequest(
            $requestMethod,
            $endpoint,
            $headers,
            http_build_query( $fields )
        );
        
        $response = $this->client->send( $request );
        
        if ( ! ( $response->getStatusCode() >= 200 && $response->getStatusCode() < 300 ) ) {
            throw HttpException::factory( $request, $response );
        }
        
        $result = [];
        parse_str( $response->getBody()->getContents(), $result );
        foreach ( $result as &$value ) {
            $value = urldecode( $value );
        }
        
        return $result;
    }
}