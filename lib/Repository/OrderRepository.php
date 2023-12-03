<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Order;

class OrderRepository extends EntityRepository
{
    public function getShoppingCartByUser( ?UserPaymentAwareInterface $user )
    {
        $shoppingCart = $this->findOneBy( ['user' => $user, 'status' => Order::STATUS_SHOPPING_CART] );
        
        return $shoppingCart;
    }
}
