<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Vankosoft\UsersBundle\Model\UserInterface;

interface CommentInterface extends ResourceInterface
{
    public function getAuthor(): ?UserInterface;
    public function getCommentSubject(): ?CommentableInterface;
    public function getComment(): ?string;
    public function getLikes(): int;
    public function addLike(): void;
    public function getDislikes(): int;
    public function addDislike(): void;
}