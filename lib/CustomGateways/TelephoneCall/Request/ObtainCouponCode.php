<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request;

use Payum\Core\Request\Generic;
use Payum\Core\Exception\LogicException;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder\CouponCodeInterface;

class ObtainCouponCode extends Generic
{
    /**
     * @var CouponCodeInterface
     */
    protected $couponCode;
    
    /**
     * @param object|null $firstModel
     * @param object|null $currentModel
     */
    public function __construct( $firstModel = null, $currentModel = null )
    {
        parent::__construct( $firstModel );
        
        $this->setModel( $currentModel );
    }
    
    /**
     * @param CouponCodeInterface $couponCode
     */
    public function set( CouponCodeInterface $couponCode )
    {
        $this->couponCode = $couponCode;
    }
    
    /**
     * @return CouponCodeInterface
     */
    public function obtain()
    {
        if ( false == $this->couponCode ) {
            throw new LogicException( 'Coupon code could not be obtained. It has to be set before obtain.' );
        }
        
        return $this->couponCode;
    }
}