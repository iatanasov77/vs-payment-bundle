<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Resource\Model\ToggleableTrait;

class PaymentMethod implements Interfaces\PaymentMethodInterface
{
    use ToggleableTrait;
    
    protected $id;
    
    protected $name;
    
    protected $gateway;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
    
    public function getGateway() 
    {
        return $this->gateway;
    }
    
    public function setGateway( $gateway )
    {
        $this->gateway  = $gateway;
        
        return $this;
    }
}
