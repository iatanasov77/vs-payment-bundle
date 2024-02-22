<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionRuleCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefault( 'entry_type', PromotionRuleType::class );
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_collection';
    }
}