<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

trait CustomerEntity
{
    /**
     * @var CustomerGroupInterface
     *
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Customer\Model\CustomerGroupInterface", inversedBy="customers")
     * @ORM\JoinColumn(name="customer_group_id", referencedColumnName="id", nullable=true)
     */
    protected $group;
    
    public function getGroup(): ?CustomerGroupInterface
    {
        return $this->group;
    }
    
    public function setGroup( ?CustomerGroupInterface $group ): void
    {
        $this->group = $group;
    }
}