<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

class TelephoneCallResponse extends \ArrayObject
{
    const STATUS_FIELD  = 'status';
    
    public function __construct()
    {
        $data = [
            self::STATUS_FIELD  => 'TEST',
        ];
        
        parent::__construct( $data, \ArrayObject::STD_PROP_LIST );
    }
    
    /*
    public function offsetSet( $key, $value ): void
    {
        
    }
    */
}