<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Security\TokenInterface;
use Payum\Core\Security\Util\Random;
use Payum\Core\Storage\IdentityInterface;

class PayumToken implements TokenInterface
{
    /**
     * @var IdentityInterface
     */
    protected $details;
    
    /**
     * @var string
     */
    protected $hash;
    
    /**
     * @var string
     */
    protected $afterUrl;
    
    /**
     * @var string
     */
    protected $targetUrl;
    
    /**
     * @var string
     */
    protected $gatewayName;
    
    public function __construct()
    {
        $this->hash = Random::generateToken();
    }
    
    /**
     * {@inheritDoc}
     *
     * @return Identity
     */
    public function getDetails()
    {
        return $this->details;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setDetails($details): void
    {
        $this->details = $details;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getHash(): string
    {
        return $this->hash;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getTargetUrl(): string
    {
        return $this->targetUrl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setTargetUrl($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getAfterUrl(): string
    {
        return $this->afterUrl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setAfterUrl($afterUrl)
    {
        $this->afterUrl = $afterUrl;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getGatewayName(): string
    {
        return $this->gatewayName;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setGatewayName($gatewayName)
    {
        $this->gatewayName = $gatewayName;
    }
}
