<?php namespace Vankosoft\PaymentBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;

use Vankosoft\PaymentBundle\Form\DataTransformer\PercentFloatToLocalizedStringTransformer;

final class PercentageDiscountConfigurationType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'percentage', PercentType::class, [
                'label'                 => 'vs_payment.form.promotion_action.percentage_discount_configuration_percentage',
                'translation_domain'    => 'VSPaymentBundle',
                'constraints'           => [
                    new NotBlank( ['groups' => ['sylius']] ),
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                    new Range( [
                        'min'               => 0,
                        'max'               => 1,
                        'notInRangeMessage' => 'sylius.promotion_action.percentage_discount_configuration.not_in_range',
                        'groups'            => ['sylius'],
                    ]),
                ],
            ])
        ;
            
        $builder->get( 'percentage' )->resetViewTransformers()->addViewTransformer( new PercentFloatToLocalizedStringTransformer() );
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_action_percentage_discount_configuration';
    }
}