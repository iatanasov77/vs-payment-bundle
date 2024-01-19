<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Gedmo\Tree\Traits\NestedSet;
use Vankosoft\PaymentBundle\Model\Comment;
use Vankosoft\PaymentBundle\Model\Interfaces\CommentInterface;

trait NestedTreeTrait
{
    use NestedSet;
    
    /** @var Comment|null */
    protected $parent;
    
    /** @var Collection<int, Comment> */
    protected $children;
    
    public function getRoot(): ?self
    {
        return $this->root;
    }
    
    public function getParent(): ?self
    {
        return $this->parent;
    }
    
    public function setParent(self $parent = null): void
    {
        $this->parent = $parent;
    }
    
    public function getChildren(): Collection
    {
        return $this->children;
    }
    
    public function hasChild(CommentInterface $taxon): bool
    {
        return $this->children->contains($taxon);
    }
    
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }
    
    public function addChild(CommentInterface $taxon): void
    {
        if (!$this->hasChild($taxon)) {
            $this->children->add($taxon);
        }
        
        if ($this !== $taxon->getParent()) {
            $taxon->setParent($this);
        }
    }
    
    public function removeChild(CommentInterface $taxon): void
    {
        if ($this->hasChild($taxon)) {
            $taxon->setParent(null);
            
            $this->children->removeElement($taxon);
        }
    }
}