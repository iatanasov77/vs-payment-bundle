<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionActionChoiceType extends AbstractType
{
    private array $actions;
    
    public function __construct( array $actions )
    {
        $this->actions = $actions;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'choices' => array_flip( $this->actions ),
        ]);
    }
    
    public function getParent(): string
    {
        return ChoiceType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_action_choice';
    }
}