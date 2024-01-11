<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Http\Message\MessageFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RuntimeException;
use Payum\Core\HttpClientInterface;

class Api
{
    /** @var HttpClientInterface */
    protected $client;
    
    /** @var MessageFactory */
    protected $messageFactory;
    
    protected $options = [
        'api_login_endpoint'            => null,
        'api_verify_coupon_endpoint'    => null,
        'username'                      => null,
        'password'                      => null,
        'useraction'                    => null,
    ];
    
    /**
     * @param HttpClientInterface|null $client
     * @param MessageFactory|null      $messageFactory
     */
    public function __construct(
        array $options,
        HttpClientInterface $client,
        MessageFactory $messageFactory
    ) {
        $options    = ArrayObject::ensureArrayObject( $options );
        $options->defaults( $this->options );
        $options->validateNotEmpty([
            'api_login_endpoint',
            'api_verify_coupon_endpoint',
            'username',
            'password',
        ]);
        
        $this->options          = $options;
        $this->client           = $client;
        $this->messageFactory   = $messageFactory;
    }
    
    /**
     * @return array
     */
    public function doLogin()
    {
        $requestFields  = $this->createLoginRequestFields();
        $response       = $this->doRequest( $requestFields );
        
        return [
            'auth' => [
                'url'       => $requestFields['endpoint'],
                'response'  => $response,
            ]
        ];
    }
    
    /**
     * @param array $fields
     *
     * @return array
     */
    public function doTelephoneCallPayment( ArrayObject $model )
    {
        $model  = ArrayObject::ensureArrayObject( $options );
        $local  = $model->getArray( 'local' );
        
        if ( false == isset( $local['pricing_plan_id'] ) ) {
            throw new RuntimeException( 'The pricing_plan_id must be set either to FormRequest.' );
        }
        
        if ( false == isset( $local['coupon_code'] ) ) {
            throw new RuntimeException( 'The coupon_code must be set either to FormRequest.' );
        }
        
        
        $requestFields  = $this->createVerifyCouponRequestFields(
            $local['pricing_plan_id'],
            $local['coupon_code'],
            $model['auth']
        );
        $response       = $this->doRequest( $requestFields );
        
        return $response;
    }
    
    /**
     * @throws HttpException
     *
     * @return array
     */
    protected function doRequest( array $fields )
    {
        $request    = $this->createPsr7Request( $fields );
        $response   = $this->client->send( $request );
        
        if ( ! ( $response->getStatusCode() >= 200 && $response->getStatusCode() < 300 ) ) {
            throw HttpException::factory( $request, $response );
        }
        
        return $response->getBody()->getContents();
    }
    
    protected function createPsr7Request( array $fields )
    {
        $headers    = [
            'Content-Type'  => 'application/json',
        ];
        
        if ( \array_key_exists( 'authToken', $fields ) ){
            $headers['Authorization']   = 'Bearer ' . $fields['authToken'];
        }
        
        // \http_build_query( $fields['body'] )
        $request = $this->messageFactory->createRequest(
            $fields['method'],
            $fields['endpoint'],
            $headers,
            \json_encode( $fields['body'] )
        );
        
        return $request;
    }
    
    /**
     * @param array $fields
     */
    protected function createLoginRequestFields()
    {
        if ( false == $this->options['api_login_endpoint'] ) {
            throw new RuntimeException( 'The api_login_endpoint must be set either to FormRequest or to options.' );
        }
        if ( false == $this->options['username'] ) {
            throw new RuntimeException( 'The username must be set either to FormRequest or to options.' );
        }
        if ( false == $this->options['password'] ) {
            throw new RuntimeException( 'The password must be set either to FormRequest or to options.' );
        }
        
        return [
            'endpoint'  => $this->options['api_login_endpoint'],
            'method'    => 'POST',
            'body'      => [
                'username' => $this->options['username'],
                'password' => $this->options['password'],
            ],
        ];
    }
    
    protected function createVerifyCouponRequestFields( string $pricingPlanId, string $couponCode, array $authResponse )
    {
        if ( false == $this->options['api_verify_coupon_endpoint'] ) {
            throw new RuntimeException( 'The api_login_endpoint must be set either to FormRequest or to options.' );
            
            $endpoint   = $this->options['api_verify_coupon_endpoint'];
        }
        
        return [
            'endpoint'  => \sprintf( '%s/%s/%s', $endpoint, $pricingPlanId, $couponCode ),
            'method'    => 'GET',
            'authToken' => $authResponse['payload']['token'],
            'body'      => [],
        ];
    }
}