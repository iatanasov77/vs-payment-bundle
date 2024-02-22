<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Filter;

use Sylius\Component\Core\Model\OrderItemInterface;

interface FilterInterface
{
    /**
     * @param OrderItemInterface[] $items
     *
     * @return OrderItemInterface[]
     */
    public function filter( array $items, array $configuration ): array;
}
