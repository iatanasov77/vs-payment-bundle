<?php namespace Vankosoft\PaymentBundle\Component\Promotion\Filter;

use Sylius\Component\Core\Model\ProductInterface;

final class TaxonFilter implements FilterInterface
{
    public function filter( array $items, array $configuration ): array
    {
        if ( empty( $configuration['filters']['taxons_filter']['taxons'] ) ) {
            return $items;
        }

        $filteredItems = [];
        foreach ( $items as $item ) {
            if ( $this->hasProductValidTaxon( $item->getProduct(), $configuration['filters']['taxons_filter']['taxons'] ) ) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }

    /**
     * @param string[] $taxonCodes
     */
    private function hasProductValidTaxon( ProductInterface $product, array $taxonCodes ): bool
    {
        foreach ( $product->getTaxons() as $taxon ) {
            if ( in_array( $taxon->getCode(), $taxonCodes, true ) ) {
                return true;
            }
        }

        return false;
    }
}
