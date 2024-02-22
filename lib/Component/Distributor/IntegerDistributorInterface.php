<?php namespace Vankosoft\PaymentBundle\Component\Distributor;

interface IntegerDistributorInterface
{
    /**
     * @throws \InvalidArgumentException
     */
    public function distribute( float $amount, int $numberOfTargets ): array;
}
