<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Doctrine\Common\Collections\Collection;

interface PaymentsUserInterface extends BaseUserInterface, ResourceInterface
{
    public function getOrders(): Collection;
}
