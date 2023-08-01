<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanCategoryInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class PricingPlanCategory implements PricingPlanCategoryInterface
{
    /** @var mixed */
    protected $id;
    
    /** @var TaxonInterface */
    protected $taxon;
    
    /** @var PricingPlanCategoryInterface */
    protected $parent;
    
    /** @var Collection|PricingPlanCategory[] */
    protected $children;
    
    /** @var Collection|PricingPlanInterface[] */
    protected $plans;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->plans    = new ArrayCollection();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon():? TaxonInterface
    {
        return $this->taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setTaxon(?TaxonInterface $taxon): void
    {
        $this->taxon = $taxon;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent( ?PricingPlanCategoryInterface $parent ): PricingPlanCategoryInterface
    {
        $this->parent = $parent;
        
        return $this;
    }
    
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    /**
     * @return Collection|PricingPlanInterface[]
     */
    public function getPlans(): Collection
    {
        return $this->plans;
    }
    
    public function addPlan( PricingPlanInterface $plan ): PricingPlanCategoryInterface
    {
        if ( ! $this->plans->contains( $plan ) ) {
            $this->plans[] = $plan;
            $plan->addCategory( $this );
        }
        
        return $this;
    }
    
    public function removePlan( PricingPlanInterface $plan ): PricingPlanCategoryInterface
    {
        if ( $this->plans->contains( $plan ) ) {
            $this->plans->removeElement( $plan );
            $plan->removeCategory( $this );
        }
        
        return $this;
    }
    
    public function getName(): string
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
    
    public function setName( string $name ) : self
    {
        if ( ! $this->taxon ) {
            // Create new taxon into the controller and set the properties passed from form
            return $this;
        }
        $this->taxon->setName( $name );
        
        return $this;
    }
    
    public function __toString()
    {
        return $this->taxon ? $this->taxon->getName() : '';
    }
}