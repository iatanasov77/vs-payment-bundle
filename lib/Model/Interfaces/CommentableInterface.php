<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;

interface CommentableInterface
{
    /** @return string */
    public function getName(): ?string;
    
    /** @return Collection|CatalogCommentInterface[] */
    public function getComments(): Collection;
    
    public function addComment(CatalogCommentInterface $comment): void;
    
    public function removeComment(CatalogCommentInterface $comment): void;
}