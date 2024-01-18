<?php namespace Vankosoft\PaymentBundle\Model;

use Sylius\Component\Review\Model\Reviewer as BaseReviewer;
use Vankosoft\PaymentBundle\Model\Interfaces\ReviewerInterface;
use Vankosoft\UsersBundle\Model\UserInterface;

class Reviewer extends BaseReviewer implements ReviewerInterface
{
    public static function fromUser( UserInterface $user ): self
    {
        $reviewer   = new self();
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
}