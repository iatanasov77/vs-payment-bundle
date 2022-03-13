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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        parent::buildForm( $builder, $options );
        
        $gatewayConfig  = $options['data'];
        
        $builder
            ->add( 'enabled', CheckboxType::class, [
                'required'=>false,
                'label' => 'Active',
            ] )
            ->add( 'useSandbox', CheckboxType::class, [
                'required'=>false,
                'label' => 'Use Sandbox',
                
            ] )
            ->add( 'gatewayName', TextType::class, [
                'label' => 'Gateway',
                
            ] )
            ->add( 'factoryName', ChoiceType::class, [
                'label' => 'Factory',
                'placeholder' => '-- Select Factory --',
                'choices'  => [
                    'offline' => 'offline',
                    'paypal_express_checkout' => 'paypal_express_checkout',
                    'paypal_pro_checkout' => 'paypal_pro_checkout',
                    'stripe_checkout' => 'stripe_checkout',
                ],
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


