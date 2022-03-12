<?php namespace IA\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use IA\PaymentBundle\Entity\PaymentMethod as PaymentMethodData;
use IA\PaymentBundle\Entity\GatewayConfig;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 */
class PaymentMethod extends AbstractType
{

    public function getName()
    {
        return 'ia_payment_paymentmethod';
    }

    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'gateway', EntityType::class, [
                'placeholder' => '-- Select Gateway --',
                'class' => GatewayConfig::class,
                'choice_label' => 'gatewayName',
            ] )
            ->add( 'name', TextType::class )
            ->add( 'route', TextType::class )
            ->add( 'active', CheckboxType::class, array('required'=>false ) )
                
            
            
            ->add('btnSave', SubmitType::class, array('label' => 'Save'))
            ->add('btnCancel', ButtonType::class, array('label' => 'Cancel'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PaymentMethodData::class
        ));
    }

}


