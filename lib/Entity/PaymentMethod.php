<?php namespace IA\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="IAP_PaymentMethods")
 */
class PaymentMethod
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=64, nullable=false)
     */
    protected $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=128, nullable=false)
     */
    protected $route;
    
    /**
     * @var bool
     * 
     * @ORM\Column(name="active", type="boolean", nullable=true)
     */
    protected $active;
    
    /**
     * @ORM\ManyToOne(targetEntity="GatewayConfig", inversedBy="paymentMethods")
     * @ORM\JoinColumn(name="gatewayId", referencedColumnName="id")
     */
    protected $gateway;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Package
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    public function setRoute($route)
    {
        $this->route = $route;
        
        return $this;
    }
    
    public function getRoute()
    {
        return $this->route;
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
    
    public function setActive($active)
    {
        $this->active = $active;
        
        return $this;
    }
    
    public function getActive()
    {
        return $this->active;
    }
}
