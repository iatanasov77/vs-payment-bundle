<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Interfaces\PaymentMethodInterface;

class GatewayConfig extends BaseGatewayConfig implements Interfaces\GatewayConfigInterface
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var bool
     */
    protected $useSandbox;
    
    /**
     * @var array
     */
    protected $sandboxConfig;
    
    /**
     * @var Collection|PaymentMethodInterface[]
     */
    protected $paymentMethods;
    
    /**
     * {@inheritDoc}
     * 
     * Override BaseGatewayConfig::getConfig() To Can Get Factory Form From Parent 
     * Or Get Stored Configuration From Database
     * NOTE: Configuration for Sandbox and Production is Set in One Record
     * 
     * @param bool $fromParent
     * @param bool $forSandbox          Maybe Not Needed
     * 
     * @return array
     */
    public function getConfig( $fromParent = false, $forSandbox = false ) 
    {
        if( $fromParent )
            return parent::getConfig();
        
        return $this->useSandbox || $forSandbox ? $this->sandboxConfig : $this->config;
    }
    
    public function __construct()
    {
        parent::__construct();
        
        $this->useSandbox       = false;
        $this->sandboxConfig    = [];
        
        $this->paymentMethods   = new ArrayCollection();
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
    
    /**
     * @return Collection|PaymentMethodInterface[]
     */
    public function getPaymentMethods()
    {
        return $this->paymentMethods;
    }
    
    public function addPaymentMethod( PaymentMethodInterface $paymentMethod )
    {
        if( ! $this->paymentMethods->contains( $paymentMethod ) ) {
            $this->paymentMethods->add( $paymentMethod );
            $paymentMethod->setGateway( $this );
            
        }
    }
    
    public function removePaymentMethod( PaymentMethodInterface $paymentMethod )
    {
        if( $this->paymentMethods->contains( $paymentMethod ) ) {
            $this->paymentMethods->removeElement( $paymentMethod );
            $paymentMethod->setGateway( null );
        }
    }
}
