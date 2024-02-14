<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class PromotionRuleType extends AbstractConfigurablePromotionElementType
{
    public function buildForm( FormBuilderInterface $builder, array $options = [] ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'type', PromotionRuleChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.type',
                'attr'  => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_rule';
    }
}