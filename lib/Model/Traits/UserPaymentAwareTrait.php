<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait UserPaymentAwareTrait
{
    /**
     * @var array
     * 
     * @ORM\Column(name="payment_details", type="json")
     */
    protected $paymentDetails   = [];
    
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $orders;
    
    public function getPaymentDetails(): array
    {
        return $this->paymentDetails;
    }
    
    public function setPaymentDetails( array $paymentDetails ): self
    {
        $this->paymentDetails   = $paymentDetails;
        
        return $this;
    }
    
    /**
     * @return Collection
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }
}
