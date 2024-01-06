<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add( 'filterByGatewayFactory', ChoiceType::class, [
                'label'                 => 'vs_payment.form.payment_filter.filter_by_gateway_factory',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.form.factory_placeholder',
                'choices'               => \array_combine( $this->factories, $this->factories ),
            ])
            
            >add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.submit',
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }
}