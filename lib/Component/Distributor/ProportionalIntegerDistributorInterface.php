<?php namespace Vankosoft\PaymentBundle\Component\Distributor;

interface ProportionalIntegerDistributorInterface
{
    public function distribute( array $integers, int $amount ): array;
}
