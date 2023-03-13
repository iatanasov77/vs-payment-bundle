<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

use Vankosoft\PaymentBundle\Form\Type\GatewayConfigType;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 */
class GatewayConfigForm extends AbstractForm
{
    /** @var array */
    protected $factories;
    
    /** @var string */
    protected $currencyClass;
    
    public function __construct(
        string $dataClass,
        array $factories,
        string $currencyClass
    ) {
        parent::__construct( $dataClass );
     
        $this->factories        = $factories;
        $this->currencyClass    = $currencyClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {       
        parent::buildForm( $builder, $options );
        
        $gatewayConfig  = $options['data'];
        
        $builder
            ->add( 'title', TextType::class, [
                'required'              => true,
                'label'                 => 'vs_payment.form.title',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
            ->add( 'description', TextType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.description',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
            ->add( 'currency', EntityType::class, [
                'label'                 => 'vs_payment.form.currency_label',
                'required'              => true,
                'class'                 => $this->currencyClass,
                'choice_label'          => 'code',
                'placeholder'           => 'vs_payment.form.currency_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            ->add( 'useSandbox', CheckboxType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.gateway_config.use_sandbox',
                'translation_domain'    => 'VSPaymentBundle',
                
            ] )
            ->add( 'gatewayName', TextType::class, [
                'label'                 => 'vs_payment.form.gateway',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
            ->add( 'factoryName', ChoiceType::class, [
                'label'                 => 'vs_payment.form.factory',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => '-- Select Factory --',
                'choices'               => \array_combine( $this->factories, $this->factories ),
            ] )
            
            ->add( 'config', GatewayConfigType::class, [
                'data'  => $gatewayConfig->getConfig( false ),
            ] )
            
            ->add( 'sandboxConfig', GatewayConfigType::class, [
                'data' => $gatewayConfig->getSandboxConfig()
            ] )
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }

    public function getName()
    {
        return 'vs_payment.gateway_config';
    }
}


