<?php namespace Vankosoft\PaymentBundle\EventSubscriber\Event;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class SubscriptionsPaymentDoneEvent
{
    public const NAME   = 'vs_payment.subscriptions_payment';
    
    /** @var array */
    private $subscriptions;
    
    public function __construct( $subscriptions )
    {
        $this->subscriptions    = $subscriptions;
    }
    
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
}