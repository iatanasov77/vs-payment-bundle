<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;

class PricingPlansSubscriptionsRepository extends EntityRepository
{
    public function getSubscriptionsByUser( UserPaymentAwareInterface $user )
    {
        return null;
    }
}