<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PaymentMethodForm extends AbstractForm
{
    /** @var string */
    protected $gatewayClass;
    
    public function __construct(
        string $dataClass,
        string $gatewayClass
    ) {
        parent::__construct( $dataClass );
        
        $this->gatewayClass = $gatewayClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'enabled', CheckboxType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.active',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
            
            ->add( 'gateway', EntityType::class, [
                'class'                 => $this->gatewayClass,
                'choice_label'          => 'gatewayName',
                'label'                 => 'vs_payment.form.payment_method.gateway',
                'placeholder'           => 'vs_payment.form.payment_method.gateway_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.payment_method.name',
                'attr'                  => ['placeholder' => 'vs_payment.form.payment_method.name'],
                'translation_domain'    => 'VSPaymentBundle',
            ] )
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }

    public function getName()
    {
        return 'vs_payment.payment_method';
    }
}


