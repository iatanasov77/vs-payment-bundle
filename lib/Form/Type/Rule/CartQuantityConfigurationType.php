<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class CartQuantityConfigurationType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'count', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion_rule.cart_quantity_configuration_count',
                'translation_domain'    => 'VSPaymentBundle',
                'constraints'           => [
                    new NotBlank( ['groups' => ['sylius']] ),
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                ],
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_cart_quantity_configuration';
    }
}