<?php namespace Vankosoft\PaymentBundle\Model;

use Vankosoft\PaymentBundle\Model\Interfaces\CommentInterface;
use Gedmo\Tree\Traits\NestedSet;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Vankosoft\PaymentBundle\Model\Interfaces\CommenterInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\CommentableInterface;

class Comment implements CommentInterface
{
    use NestedSet;
    use TimestampableTrait;
    
    /** @var int */
    protected $id;
    
    /** @var CommenterInterface */
    protected $author;
    
    /** @var CommentableInterface */
    protected $commentSubject;
    
    /** @var string */
    protected $comment;
    
    /** @var int */
    protected $likes = 0;
    
    /** @var int */
    protected $dislikes = 0;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getAuthor(): ?CommenterInterface
    {
        return $this->author;
    }
    
    public function setAuthor(?CommenterInterface $author): void
    {
        $this->author = $author;
    }
    
    public function getCommentSubject(): ?CommentableInterface
    {
        return $this->commentSubject;
    }
    
    public function setCommentSubject(?CommentableInterface $commentSubject): void
    {
        $this->commentSubject = $commentSubject;
    }
    
    public function getComment(): ?string
    {
        return $this->comment;
    }
    
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }
    
    public function getLikes(): int
    {
        return $this->likes;
    }
    
    public function setLikes(int $likes): void
    {
        $this->likes = $likes;
    }
    
    public function addLike(): void
    {
        $this->likes++;
    }
    
    public function getDislikes(): int
    {
        return $this->dislikes;
    }
    
    public function setDislikes(int $dislikes): void
    {
        $this->dislikes = $dislikes;
    }
    
    public function addDislike(): void
    {
        $this->dislikes++;
    }
}