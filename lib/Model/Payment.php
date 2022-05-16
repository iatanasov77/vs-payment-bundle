<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Payment as BasePayment;

class Payment extends BasePayment implements Interfaces\PaymentInterface
{
    protected $id;
    
    /**
     * @var \Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\PayedServiceSubscriptionPeriodInterface
     */
    protected $paidServicePeriod;
    
    /**
     * @var Interfaces\PaymentMethodInterface
     */
    protected $paymentMethod;
    
    /**
     * @var \Vankosoft\UsersSubscriptionsBundle\Model\Interfaces\SubscribedUserInterface
     */
    protected $user;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPaidServicePeriod()
    {
        return $this->paidServicePeriod;
    }
    
    public function setPaidServicePeriod($paidServicePeriod): self
    {
        $this->paidServicePeriod = $paidServicePeriod;
        
        return $this;
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
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        
        return $this;
    }
}