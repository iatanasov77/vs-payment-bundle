<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Vankosoft\PaymentBundle\Model\Comment;
use Vankosoft\PaymentBundle\Model\Interfaces\CatalogCommentInterface;

/**
 * @TODO Remove Depends on Comment Model
 */
trait NestedTreeTrait
{
    /** @var int */
    protected $root;
    
    /**
     * @var int
     */
    protected $level;
    
    /** @var int */
    protected $left;
    
    /** @var int */
    protected $right;
    
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
    
    public function hasChild(CatalogCommentInterface $entity): bool
    {
        return $this->children->contains($entity);
    }
    
    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }
    
    public function addChild(CatalogCommentInterface $entity): void
    {
        if (!$this->hasChild($entity)) {
            $this->children->add($entity);
        }
        
        if ($this !== $entity->getParent()) {
            $entity->setParent($this);
        }
    }
    
    public function removeChild(CatalogCommentInterface $entity): void
    {
        if ($this->hasChild($entity)) {
            $entity->setParent(null);
            
            $this->children->removeElement($entity);
        }
    }
}