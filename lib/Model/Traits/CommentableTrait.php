<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\CommentInterface;

trait CommentableTrait
{
    /**
     * Add Orm Mapping for this field where is used this trait
     * 
     * @var Collection|CommentInterface[]
     */
    protected $comments;
    
    /**
     * @return Collection|CommentInterface[]
     */
    public function getComments(): Collection
    {
        return $this->reviews;
    }
    
    public function addComment( CommentInterface $comment ): void
    {
        if ( ! $this->comments->contains( $comment ) ) {
            $this->comments[] = $comment;
        }
    }
    
    public function removeComment( CommentInterface $comment ): void
    {
        if ( $this->comments->contains( $comment ) ) {
            $this->comments->removeElement( $comment );
        }
    }
}