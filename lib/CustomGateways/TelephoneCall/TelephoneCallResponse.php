<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

class TelephoneCallResponse extends \ArrayObject
{
    const FIELD_STATUS          = 'status';
    const FIELD_AUTH            = 'auth';
    const FIELD_COUPON          = 'coupon';
    const FIELD_ERROR_REASON    = 'error_reason';
    
    const STATUS_OK             = 'ok';
    const STATUS_ERROR          = 'error';
    
    public function __construct( array $data )
    {
        parent::__construct( $data, \ArrayObject::STD_PROP_LIST );
    }
}