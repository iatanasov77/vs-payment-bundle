<?php namespace Vankosoft\PaymentBundle\Component\Promotion\RuleChecker;

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class HasTaxonRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'has_taxon';
    
    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible( PromotionSubjectInterface $subject, array $configuration ): bool
    {
        if ( ! isset( $configuration['taxons'] ) ) {
            return false;
        }
        
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnsupportedTypeException( $subject, OrderInterface::class );
        }
        
        /** @var OrderItemInterface $item */
        foreach ( $subject->getItems() as $item ) {
            if ( $this->hasProductValidTaxon( $item->getProduct(), $configuration ) ) {
                return true;
            }
        }
        
        return false;
    }
    
    private function hasProductValidTaxon( ProductInterface $product, array $configuration ): bool
    {
        foreach ( $product->getTaxons() as $taxon ) {
            if ( in_array( $taxon->getCode(), $configuration['taxons'], true ) ) {
                return true;
            }
        }
        
        return false;
    }
}
