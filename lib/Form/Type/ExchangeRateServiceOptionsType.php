<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExchangeRateServiceOptionsType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'key', TextType::class, [
                'required'              => true,
                'translation_domain'    => 'VSPaymentBundle',
                'label'                 => 'vs_payment.form.paid_service.attribute_name',
                'attr'                  => [
                    'placeholder'   => 'vs_users_subscriptions.form.paid_service.attribute_name_placeholder',
                ],
            ])
            
            ->add( 'value', TextType::class, [
                'required'              => true,
                'translation_domain'    => 'VSPaymentBundle',
                'label'                 => 'vs_payment.form.paid_service.attribute_value',
                'attr'                  => [
                    'placeholder'   => 'vs_users_subscriptions.form.paid_service.attribute_value_placeholder',
                ],
            ])
        ;
    }
}
