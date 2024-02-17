<?php namespace Vankosoft\PaymentBundle\Component\Distributor;

use Webmozart\Assert\Assert;

final class ProportionalIntegerDistributor implements ProportionalIntegerDistributorInterface
{
    /**
     * @param array<array-key, int> $integers
     *
     * @return array<array-key, int>
     */
    public function distribute( array $integers, int $amount ): array
    {
        Assert::allInteger( $integers );

        $total          = array_sum( $integers );

        $distributed    = $this->distributeAmounts( $integers, $amount, $total );
        $remainder      = $amount - array_sum( $distributed );

        if ( 0 === $remainder || $amount === $remainder ) {
            return $distributed;
        }

        return $this->distributeRemainder( $distributed, $remainder );
    }

    /**
     * @param array<array-key, int> $integers
     *
     * @return array<array-key, int>
     */
    private function distributeAmounts( array $integers, int $amount, int $total ): array
    {
        return array_map(
            fn ( int $item ) => 0 === $item ? 0 : (int) round( ( $item * $amount ) / $total, 0, \PHP_ROUND_HALF_DOWN ),
            $integers,
        );
    }

    /**
     * @param array<array-key, int> $distributedAmounts
     *
     * @return array<array-key, int>
     */
    private function distributeRemainder( array $distributedAmounts, int $remainder ): array
    {
        $iMax = abs( $remainder );

        $i = 0;
        foreach ( $distributedAmounts as $key => $distributedAmount ) {
            if ( $i === $iMax ) {
                break;
            }

            $distributedAmounts[$key] += $remainder >= 0 ? 1 : -1;
            ++$i;
        }

        return $distributedAmounts;
    }
}
