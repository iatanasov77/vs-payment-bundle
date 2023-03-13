<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\PaymentBundle\Model\Product;

interface ProductCategoryInterface extends ResourceInterface
{
    public function getProducts(): Collection;
    
    public function addProduct( Product $product ): ProductCategoryInterface;
    
    public function removeProduct( Product $product ): ProductCategoryInterface;
    
    public function getTaxon(): ?TaxonInterface;
    
    public function setTaxon( ?TaxonInterface $taxon ): void;
}
