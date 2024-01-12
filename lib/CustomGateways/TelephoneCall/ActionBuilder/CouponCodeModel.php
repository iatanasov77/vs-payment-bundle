<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder;

class CouponCodeModel implements CouponCodeInterface
{
    /**
     * @var string
     */
    protected $token;
    
    /**
     * @var string
     */
    protected $couponCode;
    
    /**
     * {@inheritDoc}
     */
    public function setToken($token)
    {
        $this->token = $token;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getToken()
    {
        return $this->token;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getCouponCode()
    {
        return $this->couponCode;
    }
}
