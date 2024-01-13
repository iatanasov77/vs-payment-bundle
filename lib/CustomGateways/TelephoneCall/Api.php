<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Http\Message\MessageFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\Http\HttpException;
use Payum\Core\Exception\RuntimeException;
use Payum\Core\HttpClientInterface;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallResponse;

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
    public function doLogin(): array
    {
        $requestFields  = $this->createLoginRequestFields();
        $response       = $this->doRequest( $requestFields );
        
        return [
            TelephoneCallResponse::FIELD_AUTH => [
                'url'       => $requestFields['endpoint'],
                'response'  => \json_decode( $response, true )
            ]
        ];
    }
    
    /**
     * @param array $fields
     *
     * @return array
     */
    public function doTelephoneCallPayment( ArrayObject $model ): array
    {
        $model  = ArrayObject::ensureArrayObject( $model );
        $local  = $model->getArray( 'local' );
        
        if ( false == isset( $local['pricing_plan_id'] ) ) {
            throw new RuntimeException( 'The pricing_plan_id must be set.' );
        }
        
        if ( false == isset( $model['coupon_code'] ) ) {
            throw new RuntimeException( 'The coupon_code must be set either to FormRequest.' );
        }
        
        
        $requestFields  = $this->createVerifyCouponRequestFields(
            $local['pricing_plan_id'],
            $model['coupon_code'],
            $this->getAuthToken( $model )
        );
        $response       = $this->doRequest( $requestFields );
        
        return \json_decode( $response, true );
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
    
    protected function createVerifyCouponRequestFields( string $pricingPlanId, string $couponCode, string $authToken )
    {
        if ( false == $this->options['api_verify_coupon_endpoint'] ) {
            throw new RuntimeException( 'The api_login_endpoint must be set either to FormRequest or to options.' );
        }
        
        return [
            'endpoint'  => $this->options['api_verify_coupon_endpoint'],
            'method'    => 'POST',
            'authToken' => $authToken,
            'body'      => [
                'pricingPlanId' => $pricingPlanId,
                'couponCode'    => $couponCode,
            ],
        ];
    }
    
    protected function getAuthToken( ArrayObject $model ): string
    {
        $modelAuth  = $model[TelephoneCallResponse::FIELD_AUTH];
        
        return $modelAuth['response']['payload']['token'];
    }
}