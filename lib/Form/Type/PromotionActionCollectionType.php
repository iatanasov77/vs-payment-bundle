<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionActionCollectionType extends AbstractConfigurationCollectionType
{
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefault( 'entry_type', PromotionActionType::class );
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_action_collection';
    }
}