<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class PricingPlansSubscriptionsRepository extends EntityRepository
{
    public function getActiveSubscriptionsByUser( ?UserPaymentAwareInterface $user )
    {
        if ( ! $user ) {
            return [];
        }
        
        $qb = $this->createQueryBuilder( 'pps' )
                    ->innerJoin( 'pps.user', 'u' )
                    ->where( 'u.id = :userId' )
                    ->where( 'pps.active = 1' )
                    ->setParameter( 'userId', $user->getId() );
        
        return $qb->getQuery()->getResult();
    }
    
    public function getSubscriptionsByUser( ?UserPaymentAwareInterface $user )
    {
        $subscriptions  = [];
        if ( ! $user ) {
            return $subscriptions;
        }
        
        $collection     = $user->getPricingPlanSubscriptions();
        foreach ( $collection as $subscription ) {
            $subscriptions[$subscription->getServiceCode()]    = $subscription;
        }
        
        return $subscriptions;
    }
    
    public function getSubscribedServicesByUser( ?UserPaymentAwareInterface $user )
    {
        $subscriptions  = [];
        if ( ! $user ) {
            return $subscriptions;
        }
        
        $collection     = $user->getPricingPlanSubscriptions();
        foreach ( $collection as $subscription ) {
            if ( ! isset( $subscriptions[$subscription->getServiceCode()] ) ) {
                $subscriptions[$subscription->getServiceCode()] = [];
            }
            $subscriptions[$subscription->getServiceCode()][$subscription->getPeriodCode()]    = $subscription;
        }
        
        return $subscriptions;
    }
    
    public function getSubscriptionByUserOnPricingPlan( ?UserPaymentAwareInterface $user, PricingPlanInterface $pricingPlan )
    {
        if ( ! $user ) {
            return null;
        }
        
        $subscription   = $this->findOneBy( ['user' => $user, 'pricingPlan' => $pricingPlan] );
        
        return $subscription;
    }
}