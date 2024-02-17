<?php namespace Vankosoft\PaymentBundle\Component\Distributor;

use Sylius\Component\Core\Model\ChannelInterface;

interface MinimumPriceDistributorInterface
{
    public function distribute( array $orderItems, int $amount, ChannelInterface $channel, bool $appliesOnDiscounted ): array;
}
