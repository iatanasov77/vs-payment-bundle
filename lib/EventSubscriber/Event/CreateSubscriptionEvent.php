<?php namespace Vankosoft\PaymentBundle\EventSubscriber\Event;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class CreateSubscriptionEvent
{
    public const NAME   = 'vs_payment.create_subscription';
    
    /** @var object */
    private $pricingPlan;
    
    public function __construct( $pricingPlan )
    {
        $this->pricingPlan    = $pricingPlan;
    }
    
    public function getPricingPlan()
    {
        return $this->pricingPlan;
    }
}