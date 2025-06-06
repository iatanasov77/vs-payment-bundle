<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

trait UserPaymentAwareEntity
{
    /** @var array */
    #[ORM\Column(name: "payment_details", type: "json", nullable: true)]
    protected $paymentDetails   = [];
    
    /** @var Collection | Order[] */
    #[ORM\OneToMany(targetEntity: "Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface", mappedBy: "user", cascade: ["persist", "remove"], orphanRemoval: true)]
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
