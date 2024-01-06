<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\PaymentBundle\Component\Payment\Payment;

class PaymentFilterForm extends AbstractType
{
    /** @var array */
    protected $factories;
    
    public function __construct( Payment $vsPayment )
    {
        $this->factories    = $vsPayment->availableFactories();
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'number', TextType::class, [
                'label'                 => 'vs_payment.form.payment_filter.payment_number',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'description', TextType::class, [
                'label'                 => 'vs_payment.form.payment_filter.payment_description',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'filterByGatewayFactory', ChoiceType::class, [
                'label'                 => 'vs_payment.form.payment_filter.gateway_factory',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.form.factory_placeholder',
                'choices'               => \array_combine( $this->factories, $this->factories ),
                'required'              => false,
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_application.form.search',
                'translation_domain'    => 'VSApplicationBundle',
            ])
        ;
    }
}