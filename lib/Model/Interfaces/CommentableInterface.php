<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;

interface CommentableInterface
{
    /** @return string */
    public function getName(): ?string;
    
    /** @return Collection|CommentInterface[] */
    public function getComments(): Collection;
    
    public function addComment(CommentInterface $comment): void;
    
    public function removeComment(CommentInterface $comment): void;
}