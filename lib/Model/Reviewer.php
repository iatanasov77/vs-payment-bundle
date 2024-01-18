<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Review\Model\Reviewer as BaseReviewer;
use Vankosoft\PaymentBundle\Model\Interfaces\ReviewerInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\ReviewerAwareInterface;

class Reviewer extends BaseReviewer implements ReviewerInterface
{
    /** @var ReviewerAwareInterface */
    protected $user;
    
    public static function fromUser( ReviewerAwareInterface $user ): self
    {
        $reviewer   = new self();
        $reviewer->user         = $user;
        
        $reviewer->id           = $user->getId();
        $reviewer->email        = $user->getEmail();
        $reviewer->firstName    = $user->getInfo() ? $user->getInfo()->getFirstName() : 'UNDEFINED';
        $reviewer->lastName     = $user->getInfo() ? $user->getInfo()->getLastName() : 'UNDEFINED';
        
        return $reviewer;
    }
    
    public function getFullName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    public function getUser(): ReviewerAwareInterface
    {
        return $this->user;
    }
    
    public function setUser( ReviewerAwareInterface $user ): void
    {
        $this->user = $user;
    }
}