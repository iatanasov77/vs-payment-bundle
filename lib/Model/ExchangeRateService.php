<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\ExchangeRateServiceInterface;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    /** @var int */
    protected $id;
    
    /** @var string */
    protected $title;
    
    /** @var string */
    protected $serviceId;
    
    /** @var array */
    protected $options;
    
    public function __construct()
    {
        $this->options  = [];
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setTitle($title): self
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getServiceId()
    {
        return $this->serviceId;
    }
    
    public function setServiceId($serviceId): self
    {
        $this->serviceId = $serviceId;
        
        return $this;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setOptions( array $options ): self
    {
        $this->options = $options;
        
        return $this;
    }
}
