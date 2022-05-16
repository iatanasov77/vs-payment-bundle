<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;
use Sylius\Component\Resource\Model\ToggleableTrait;

class GatewayConfig extends BaseGatewayConfig implements Interfaces\GatewayConfigInterface
{
    use ToggleableTrait;
    
    protected $id;
    
    protected $useSandbox;
    
    protected $sandboxConfig;
    
    protected $paymentMethods;
    
    /**
     * 
     *  Configuration for Sandbox and Production is Set in One Record,
     * 
     * @param type $builder
     * @return type
     */
    public function getConfig( $builder = true ) 
    {
        if( ! $builder )
            return parent::getConfig();
        
        return $this->useSandbox ? $this->sandboxConfig : $this->config;
    }
    
    public function __construct()
    {
        parent::__construct();
        
        $this->useSandbox       = false;
        $this->sandboxConfig    = [];
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getSandboxConfig()
    {
        return $this->sandboxConfig;
    }
    
    public function setSandboxConfig( array $sandboxConfig ): self
    {
        $this->sandboxConfig = $sandboxConfig;
        
        return $this;
    }
    
    public function getUseSandbox()
    {
        return $this->useSandbox;
    }

    public function setUseSandbox($useSandbox): self
    {
        $this->useSandbox = $useSandbox;
        
        return $this;
    }
}
