<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionRuleChoiceType extends AbstractType
{
    private array $rules;
    
    public function __construct( array $rules )
    {
        $this->rules = $rules;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'choices' => array_flip( $this->rules ),
        ]);
    }
    
    public function getParent(): string
    {
        return ChoiceType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_choice';
    }
}