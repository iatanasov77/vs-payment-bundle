<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PaymentFilterForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'filterByGatewayFactory', ChoiceType::class, [
                'label'                 => 'vs_payment.form.payment_filter.filter_by_gateway_factory',
                'translation_domain'    => 'VSPaymentBundle',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            >add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.submit',
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }
}