<?php namespace Vankosoft\PaymentBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductCategoryInterface;

class ProductCategory extends ProductCategoryInterface
{
    /** @var mixed */
    protected $id;
    
    /** @var ProductCategoryInterface */
    protected $parent;
    
    /** @var Collection|ProductCategory[] */
    protected $children;
    
    /** @var Collection|Product[] */
    protected $products;
    
    /** @var TaxonInterface */
    protected $taxon;
    
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->products = new ArrayCollection();
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
    public function getParent(): ?ProductCategoryInterface
    {
        return $this->parent;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setParent(?ProductCategoryInterface $parent): ProductCategoryInterface
    {
        $this->parent = $parent;
        
        return $this;
    }
    
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    public function getProducts(): Collection
    {
        return $this->products;
    }
    
    public function addProduct( Product $product ): ProductCategoryInterface
    {
        if ( ! $this->products->contains( $product ) ) {
            $this->products[] = $product;
            $product->addCategory( $this );
        }
        
        return $this;
    }
    
    public function removeProduct( Product $product ): ProductCategoryInterface
    {
        if ( $this->products->contains( $product ) ) {
            $this->products->removeElement( $product );
            $product->removeCategory( $this );
        }
        
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTaxon(): ?TaxonInterface
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
    
    public function getName()
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
