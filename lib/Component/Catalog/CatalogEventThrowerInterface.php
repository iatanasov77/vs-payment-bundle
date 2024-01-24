<?php namespace Vankosoft\PaymentBundle\Component\Catalog;

interface CatalogEventThrowerInterface
{
    public function triggerSubscriptionsPaymentDone( $subscriptions, $payment ): void;
}