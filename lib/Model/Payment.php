<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Payment as BasePayment;
use Sylius\Component\Resource\Model\TimestampableTrait;

class Payment extends BasePayment implements Interfaces\PaymentInterface
{
    use TimestampableTrait;
    
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var float
     */
    protected $realAmount;
    
    /**
     * @var \Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface
     */
    protected $order;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getRealAmount()
    {
        return $this->realAmount;
    }
    
    public function setRealAmount($realAmount): self
    {
        $this->realAmount = $realAmount;
        
        return $this;
    }
    
    public function getOrder()
    {
        return $this->order;
    }
    
    public function setOrder($order): self
    {
        $this->order = $order;
        $order->setPayment( $this );
        
        return $this;
    }
    
    public function getFactoryName()
    {
        return $this->order ?
            $this->order->getPaymentMethod()->getGateway()->getFactoryName() :
            '';
    }
    
    public function getGatewayName()
    {
        return $this->order ?
                $this->order->getPaymentMethod()->getGateway()->getGatewayName() :
                '';
    }
}