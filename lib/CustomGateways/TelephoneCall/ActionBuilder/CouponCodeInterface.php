<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder;

interface CouponCodeInterface
{
    /**
     * @return string
     */
    public function getToken();
    
    /**
     * @param string $token
     */
    public function setToken($token);
    
    /**
     * @return string
     */
    public function getCouponCode();
    
    /**
     * @param string $brand
     */
    public function setCouponCode($couponCode);
}
