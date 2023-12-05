<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class PricingPlansSubscriptionsRepository extends EntityRepository
{
    public function getSubscriptionsByUser( UserPaymentAwareInterface $user )
    {
        $collection     = $user->getPricingPlanSubscriptions();
        
        $subscriptions  = [];
        foreach ( $collection as $subscription ) {
            $subscriptions[$subscription->getServiceCode()]    = $subscription;
        }
        
        return $subscriptions;
    }
    
    public function getSubscribedServicesByUser( UserPaymentAwareInterface $user )
    {
        $collection     = $user->getPricingPlanSubscriptions();
        
        $subscriptions  = [];
        foreach ( $collection as $subscription ) {
            if ( ! isset( $subscriptions[$subscription->getServiceCode()] ) ) {
                $subscriptions[$subscription->getServiceCode()] = [];
            }
            $subscriptions[$subscription->getServiceCode()][$subscription->getPeriodCode()]    = $subscription;
        }
        
        return $subscriptions;
    }
    
    public function getSubscriptionByUserOnPricingPlan( UserPaymentAwareInterface $user, PricingPlanInterface $pricingPlan )
    {
        $subscription   = $this->findOneBy( ['user' => $user, 'pricingPlan' => $pricingPlan] );
        
        return $subscription;
    }
}