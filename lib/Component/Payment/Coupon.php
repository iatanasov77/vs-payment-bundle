<?php namespace Vankosoft\PaymentBundle\Component\Payment;

class Coupon
{
    const DISCOUNT_COUPON_TYPE  = 'discount_coupon';
    const PAYMENT_COUPON_TYPE   = 'payment_coupon';
    
    public function getCouponTypeChoices(): array
    {
        return [
            self::DISCOUNT_COUPON_TYPE  => 'vs_payment.form.coupon.discount_coupon',
            self::PAYMENT_COUPON_TYPE   => 'vs_payment.form.coupon.payment_coupon',
        ];
    }
}