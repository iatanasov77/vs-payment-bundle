<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class NthOrderConfigurationType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'nth', IntegerType::class, [
                'label'         => 'sylius.form.promotion_rule.nth_order_configuration.nth',
                'constraints'   => [
                    new NotBlank( ['groups' => ['sylius']] ),
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_nth_order_configuration';
    }
}
