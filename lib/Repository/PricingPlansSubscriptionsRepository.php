<?php namespace Vankosoft\PaymentBundle\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class PricingPlansSubscriptionsRepository extends EntityRepository
{
    /*
     * MANUAL: https://www.boxuk.com/insight/filtering-associations-with-doctrine-2/
     *          THERE IS AN EXAMPLE HOW TO FILTER COLLECTION IN ENTITY CLASS
     */
    public function getActiveSubscriptionsByUser( ?UserPaymentAwareInterface $user )
    {
        if ( ! $user ) {
            return [];
        }
        
        $qb = $this->createQueryBuilder( 'pps' )
                    ->innerJoin( 'pps.user', 'u' )
                    ->where( 'u.id = :userId' )
                    ->andWhere( 'pps.active = 1' )
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
    
    public function getSubscriptionsByUserOnPricingPlan( ?UserPaymentAwareInterface $user, PricingPlanInterface $pricingPlan )
    {
        if ( ! $user ) {
            return [];
        }
        
        $subscriptions  = $this->findBy( ['user' => $user, 'pricingPlan' => $pricingPlan] );
        
        return $subscriptions;
    }
}