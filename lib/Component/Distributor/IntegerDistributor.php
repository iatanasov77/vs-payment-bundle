<?php namespace Vankosoft\PaymentBundle\Component\Distributor;

use Webmozart\Assert\Assert;

final class IntegerDistributor implements IntegerDistributorInterface
{
    public function distribute( float $amount, int $numberOfTargets ): array
    {
        Assert::true( ( 1 <= $numberOfTargets ), 'Number of targets must be bigger than 0.' );

        $sign       = $amount < 0 ? -1 : 1;
        $amount     = abs( $amount );

        $low        = (int) ( $amount / $numberOfTargets );
        $high       = $low + 1;

        $remainder  = $amount % $numberOfTargets;
        $result     = [];

        for ( $i = 0; $i < $remainder; ++$i ) {
            $result[] = $high * $sign;
        }

        for ( $i = $remainder; $i < $numberOfTargets; ++$i ) {
            $result[] = $low * $sign;
        }

        return $result;
    }
}
