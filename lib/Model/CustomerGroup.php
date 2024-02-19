<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Customer\Model\CustomerGroupInterface;
use Vankosoft\ApplicationBundle\Model\Traits\TaxonDescendentTrait;
use Vankosoft\PaymentBundle\Model\Interfaces\CustomerInterface;

class CustomerGroup implements CustomerGroupInterface, \Stringable
{
    use TaxonDescendentTrait {
        getCode as traitGetCode;
        setCode as traitSetCode;
        getName as traitGetName;
        setName as traitSetName;
    }
    
    /** @var mixed */
    protected $id;
    
    /** @var Collection|CustomerInterface[] */
    protected $customers;
    
    public function __construct()
    {
        $this->customers    = new ArrayCollection();
    }
    
    public function __toString(): string
    {
        return (string) $this->getName();
    }
    
    public function getCode(): ?string
    {
        return $this->traitGetCode();
    }
    
    public function setCode( ?string $code ): void
    {
        $this->traitSetCode( $code );
    }
    
    public function getName(): ?string
    {
        return $this->traitGetName();
    }
    
    public function setName( ?string $name ): void
    {
        $this->traitSetName( $name );
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return Collection|CustomerInterface[]
     */
    public function getCustomers(): Collection
    {
        return $this->customers;
    }
    
    public function addCustomer( CustomerInterface $customer ): self
    {
        if ( ! $this->customers->contains( $customer ) ) {
            $this->customers[] = $customer;
            $customer->setGroup( $this );
        }
        
        return $this;
    }
    
    public function removeCustomer( CustomerInterface $customer ): self
    {
        if ( $this->customers->contains( $customer ) ) {
            $this->customers->removeElement( $customer );
            $customer->setGroup( null );
        }
        
        return $this;
    }
}
