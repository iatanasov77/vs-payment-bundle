<?php namespace Vankosoft\PaymentBundle\EventSubscriber\Event;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class CreateNewUserSubscriptionEvent
{
    public const NAME   = 'vs_payment.create_new_user_subscription';
    
    /** @var object */
    private $user;
    
    /** @var object */
    private $pricingPlan;
    
    public function __construct( $user, $pricingPlan )
    {
        $this->user         = $user;
        $this->pricingPlan  = $pricingPlan;
    }
    
    public function getUser()
    {
        return $this->user;
    }
    
    public function getPricingPlan()
    {
        return $this->pricingPlan;
    }
}