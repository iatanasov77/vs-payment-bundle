<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vankosoft\PaymentBundle\Form\Type\PaymentMethodType;

class SelectPaymentMethodForm extends AbstractType
{
    /** @var string */
    private $paymentMethodClass;
    
    public function __construct(
        string $paymentMethodClass
    ) {
        $this->paymentMethodClass   = $paymentMethodClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'pricingPlan', HiddenType::class )
            
            ->add( 'paymentMethod', PaymentMethodType::class, [
                'paymentMethodClass'    => $this->paymentMethodClass
            ] )
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.submit',
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }
}