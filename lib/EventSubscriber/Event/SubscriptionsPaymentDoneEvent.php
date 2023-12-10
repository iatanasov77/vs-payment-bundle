<?php namespace Vankosoft\PaymentBundle\EventSubscriber\Event;

use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class SubscriptionsPaymentDoneEvent
{
    public const NAME   = 'vs_payment.subscriptions_payment';
    
    /** @var array */
    private $subscriptions;
    
    /** @var PaymentInterface */
    private $payment;
    
    public function __construct( $subscriptions, $payment )
    {
        $this->subscriptions    = $subscriptions;
        $this->payment          = $payment;
    }
    
    public function getSubscriptions()
    {
        return $this->subscriptions;
    }
    
    public function getPayment()
    {
        return $this->payment;
    }
}