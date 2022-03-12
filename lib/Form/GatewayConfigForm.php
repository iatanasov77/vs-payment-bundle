<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

use Vankosoft\PaymentBundle\Form\Type\GatewayConfigType;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 */
class GatewayConfigForm extends AbstractType
{

    public function getName()
    {
        return 'ia_payment_gateway_config';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {       
        $gatewayConfig = $options['data'];
        $builder
            ->add('gatewayName', TextType::class, array('label' => 'Gateway'))
            ->add('useSandbox', CheckboxType::class, array('required'=>false))
            ->add('factoryName', ChoiceType::class, [
                'label' => 'Factory',
                'placeholder' => '-- Select Factory --',
                'choices'  => [
                    'offline' => 'offline',
                    'paypal_express_checkout' => 'paypal_express_checkout',
                    'paypal_pro_checkout' => 'paypal_pro_checkout',
                    'stripe_checkout' => 'stripe_checkout',
                ],
            ])
            
            // CurrencyType::class
            ->add('currency', ChoiceType::class, [
                'label' => 'Merchant Account Currency',
                'placeholder' => '-- Select Currency --',
                'choices'  => [
                    'Euro' => 'EUR',
                    'US Dolar' => 'USD',
                    'Bulgarian Lev' => 'BGN',
                ],
            ])
            
            
                
            ->add('config', GatewayConfigType::class, array('data' => $gatewayConfig->getConfig(false)))
            ->add('sandboxConfig', GatewayConfigType::class, array('data' => $gatewayConfig->getSandboxConfig()))
            
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Vankosoft\PaymentBundle\Entity\GatewayConfig'
        ));
    }

}


