<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Vankosoft\PaymentBundle\Form\Type\CustomerGroupCodeChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

final class CustomerGroupConfigurationType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'group_code', CustomerGroupCodeChoiceType::class, [
                'label'         => 'sylius.form.promotion_rule.customer_group.group',
                'constraints'   => [
                    new NotBlank( ['groups' => ['sylius']] ),
                    new Type( ['type' => 'string', 'groups' => ['sylius']] ),
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_customer_group_configuration';
    }
}
