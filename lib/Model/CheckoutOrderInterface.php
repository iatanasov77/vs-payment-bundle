<?php

namespace Vankosoft\PaymentBundle\Model;

interface CheckoutOrderInterface
{
    public function getPrice();
    
    public function getDescription();
    
    public function getCurrency();
    
    public function getBillingPeriod();
    
    public function getBillingFrequency();
}
    
