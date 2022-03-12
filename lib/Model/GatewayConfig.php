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
     * ÐŸÑ€ÐµÐ´ÐµÑ„Ð¸Ð½Ð¸Ñ€Ð°Ð¼ Ð¾Ñ€Ð¸Ð³Ð¸Ð½Ð°Ð»Ð½Ð°Ñ‚Ð° Ñ„ÑƒÐ½ÐºÑ†Ð¸Ñ� , Ð·Ð° Ð´Ð° Ð¼Ð¾Ð¶Ðµ ÐºÐ¾Ð³Ð°Ñ‚Ð¾ Ñ�Ðµ Ð¸Ð·Ð²Ð¸ÐºÐ²Ð° Ð·Ð° Ð±Ð¸Ð»Ð´Ð²Ð°Ð½Ðµ Ð½Ð° Gateway Ð´Ð° Ð²Ñ€ÑŠÑ‰Ð° ÐºÐ¾Ð½Ñ„Ð¸Ð³Ð° Ñ�Ð¿Ð¾Ñ€ÐµÐ´ Ñ„Ð»Ð°Ð³Ð° useSandbox,
     * Ð° ÐºÐ¾Ð³Ð°Ñ‚Ð¾ Ñ�Ðµ Ð¸Ð·Ð²Ð¸ÐºÐ²Ð° Ð¾Ñ‚ Ñ„Ð¾Ñ€Ð¼Ð°Ñ‚Ð° Ð·Ð° Ñ€ÐµÐ´Ð°ÐºÑ†Ð¸Ñ� Ð´Ð° Ð½Ðµ Ð²Ð·Ð¸Ð¼Ð° Ð¿Ñ€ÐµÐ´Ð²Ð¸Ð´ Ñ‚Ð¾Ð·Ð¸ Ñ„Ð»Ð°Ð³
     * 
     * @param type $builder
     * @return type
     */
    public function getConfig($builder = true) 
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
