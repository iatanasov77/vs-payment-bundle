<?php
namespace Vankosoft\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\GatewayConfig as BaseGatewayConfig;

/**
 * @ORM\Table(name="IAP_GatewayConfig")
 * @ORM\Entity
 */
class GatewayConfig extends BaseGatewayConfig
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;
    
    /**
     * @var bool
     * 
     *  @ORM\Column(name="useSandbox", type="boolean", nullable=false)
     */
    protected $useSandbox;
    
    /**
     * @var array
     * 
     * @ORM\Column(name="sandboxConfig", type="json", nullable=false)
     */
    protected $sandboxConfig;
    
    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="string", length=4, nullable=true)
     */
    protected $currency;
    
    /**
     * @ORM\OneToMany(targetEntity="PaymentMethod", mappedBy="gateway", cascade={"persist"})
     */
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
        if(!$builder)
            return parent::getConfig();
        
        return $this->useSandbox ? $this->sandboxConfig : $this->config;
    }
    
    
    public function __construct()
    {
        parent::__construct();
        $this->useSandbox = false;
        $this->sandboxConfig = [];
    }
    
    function getUseSandbox()
    {
        return $this->useSandbox;
    }

    function getSandboxConfig()
    {
        return $this->sandboxConfig;
    }

    function setUseSandbox($useSandbox)
    {
        $this->useSandbox = $useSandbox;
        return $this;
    }

    function setSandboxConfig( array $sandboxConfig )
    {
        $this->sandboxConfig = $sandboxConfig;
        return $this;
    }
    
    public function getCurrency()
    {
        return $this->currency;
    }
    
    public function setCurrency( $currency )
    {
        $this->currency = $currency;
        
        return $this;
    }
}
