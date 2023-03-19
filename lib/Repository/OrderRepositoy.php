<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class OrderRepositoy extends EntityRepository
{
    public function getShoppingCart( $user, $sessionId )
    {
        return null;
    }
}
