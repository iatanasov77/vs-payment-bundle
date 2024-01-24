<?php namespace Vankosoft\PaymentBundle\Component\Catalog;

interface CatalogBridgeInterface
{
    public function getFactory();
    public function getRepository();
    public function triggerSubscriptionsPaymentDone( $subscriptions, $payment ): void;
}