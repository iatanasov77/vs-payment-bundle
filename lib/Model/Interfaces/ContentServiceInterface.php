<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CommentableInterface;

interface ContentServiceInterface extends ResourceInterface, TranslatableInterface, ReviewableInterface, CommentableInterface
{
    public function getSlug(): ?string;
    public function getTitle();
    public function getDescription();
    public function getPublished(): ?bool;
}