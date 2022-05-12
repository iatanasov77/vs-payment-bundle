<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Payment as BasePayment;

class Payment extends BasePayment implements Interfaces\PaymentInterface
{
    protected $id;
    
    protected $paymentMethod;
    
    protected $paymentDetails;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        
        return $this;
    }
    
    /*
     * @NOTE Not need i think but see more on Payum\Core\Model\ArrayObject
     */
    public function getPaymentDetails()
    {
        return $this->paymentDetails;
    }
    
    /* 
     * @NOTE Not need i think but see more on Payum\Core\Model\ArrayObject
     */
    public function setPaymentDetails($paymentDetails)
    {
        $this->paymentDetails = $paymentDetails;
        
        return $this;
    }
}