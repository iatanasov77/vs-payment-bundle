<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Doctrine\Common\Collections\Collection;
use Vankosoft\UsersBundle\Model\UserInterface;

interface UserPaymentAwareInterface extends UserInterface
{
    public function getPaymentDetails(): array;
    public function getOrders(): Collection;
}
