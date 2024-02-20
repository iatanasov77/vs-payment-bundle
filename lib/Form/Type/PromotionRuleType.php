<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class PromotionRuleType extends AbstractConfigurablePromotionElementType
{
    public function buildForm( FormBuilderInterface $builder, array $options = [] ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'type', PromotionRuleChoiceType::class, [
                'label'                 => 'vs_payment.form.promotion.rule_type',
                'placeholder'           => 'vs_payment.form.promotion.rule_type_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'                  => [
                    'data-form-collection' => 'update',
                ],
                
                'required'              => false,
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule';
    }
}