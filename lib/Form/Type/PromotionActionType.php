<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

final class PromotionActionType extends AbstractConfigurablePromotionElementType
{
    public function buildForm( FormBuilderInterface $builder, array $options = [] ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'type', PromotionActionChoiceType::class, [
                'label'                 => 'vs_payment.form.promotion.action_type',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'                  => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_action';
    }
}