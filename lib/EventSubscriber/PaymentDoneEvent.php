<?php namespace Vankosoft\PaymentBundle\EventSubscriber;

/**
 * MANUAL: https://q.agency/blog/custom-events-with-symfony5/
 */
final class PaymentDoneEvent
{
    public const NAME   = 'vs_payment.checkout_action';
    
    public const ACTION_CREATE_SUBSCRIPTION = 'action_create_subscription';
    public const ACTION_PAY_SUBSCRIPTION    = 'action_pay_subscription';
    
    /** @var object */
    private $resource;
    
    /** @var string */
    private $action;
    
    public function __construct( $resource, string $action)
    {
        $this->resource     = $resource;
        $this->action       = $action;
    }
    
    public function getResource()
    {
        return $this->resource;
    }
    
    public function getAction()
    {
        return $this->action;
    }
}