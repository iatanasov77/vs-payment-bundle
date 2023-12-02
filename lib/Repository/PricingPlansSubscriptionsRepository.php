<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;

class PricingPlansSubscriptionsRepository extends EntityRepository
{
    public function getSubscriptionsByUser( UserPaymentAwareInterface $user )
    {
        $collection     = $user->getPricingPlanSubscriptions();
        
        $subscriptions  = [];
        foreach ( $collection as $subscription ) {
            $collection[$subscription->getServiceCode()]    = $subscription;
        }
        
        return $subscriptions;
    }
}