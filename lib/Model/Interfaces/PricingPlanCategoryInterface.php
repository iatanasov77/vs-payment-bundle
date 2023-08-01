<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;

interface PricingPlanCategoryInterface extends ResourceInterface
{
    /** @TODO Remove Theese When Extend VankosoftCategoryInterface */
    public function getName(): string;
    public function getTaxon():? TaxonInterface;
    public function getParent();
    public function getChildren(): Collection;
    
    public function getPlans(): Collection;
}