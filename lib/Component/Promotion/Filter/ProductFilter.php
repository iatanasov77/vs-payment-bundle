<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Filter;

final class ProductFilter implements FilterInterface
{
    public function filter( array $items, array $configuration ): array
    {
        if ( empty( $configuration['filters']['products_filter']['products'] ) ) {
            return $items;
        }

        $filteredItems = [];
        foreach ( $items as $item ) {
            if ( in_array( $item->getProduct()->getCode(), $configuration['filters']['products_filter']['products'], true ) ) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }
}
