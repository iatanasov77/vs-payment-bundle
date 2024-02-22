<?php namespace Vankosoft\PaymentBundle\Component\Promotion\RuleChecker;

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

use Vankosoft\ApplicationBundle\Repository\TaxonRepository;
use Vankosoft\ApplicationBundle\Model\Interfaces\TaxonInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderItemInterface;

final class TotalOfItemsFromTaxonRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'total_of_items_from_taxon';
    
    /** @var TaxonRepository */
    private $taxonRepository;
    
    public function __construct( TaxonRepository $taxonRepository )
    {
        $this->taxonRepository  = $taxonRepository;
    }
    
    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible( PromotionSubjectInterface $subject, array $configuration ): bool
    {
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnsupportedTypeException( $subject, OrderInterface::class );
        }
        
        $channelCode = $subject->getChannel()->getCode();
        if ( ! isset( $configuration[$channelCode] ) ) {
            return false;
        }
        
        $configuration = $configuration[$channelCode];
        
        if ( ! isset( $configuration['taxon'] ) || ! isset( $configuration['amount'] ) ) {
            return false;
        }
        
        $targetTaxon = $this->taxonRepository->findOneBy( ['code' => $configuration['taxon']] );
        if ( ! $targetTaxon instanceof TaxonInterface ) {
            return false;
        }
        
        $itemsWithTaxonTotal = 0;
        
        /** @var OrderItemInterface $item */
        foreach ( $subject->getItems() as $item ) {
            if ( $item->getProduct()->hasTaxon( $targetTaxon ) ) {
                $itemsWithTaxonTotal += $item->getTotal();
            }
        }
        
        return $itemsWithTaxonTotal >= $configuration['amount'];
    }
}
