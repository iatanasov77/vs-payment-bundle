<?php namespace Vankosoft\PaymentBundle\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Vankosoft\PaymentBundle\Model\Interfaces\CommentInterface;

trait CommenterTrait
{
    /**
     * @var Collection
     * 
     * @ORM\OneToMany(targetEntity="Vankosoft\PaymentBundle\Model\Interfaces\CommentInterface", mappedBy="author", cascade={"persist", "remove"})
     */
    protected $comments;
    
    /**
     * @return Collection|SubscriptionInterface[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
    
    public function setComments( Collection $comments ): self
    {
        $this->comments  = $comments;
        
        return $this;
    }
    
    public function addComment( CommentInterface $comment ): self
    {
        if ( ! $this->comments->contains( $comment ) ) {
            $this->comments[]    = $comment;
            $comment->setAuthor( $this );
        }
        
        return $this;
    }
    
    public function removeComment( CommentInterface $comment ): self
    {
        if ( $this->comments->contains( $comment ) ) {
            $this->comments->removeElement( $comment );
            $comment->setAuthor( null );
        }
        
        return $this;
    }
}
