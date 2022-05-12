<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\PaymentBundle\Entity\PaymentMethod as PaymentMethodData;
use Vankosoft\PaymentBundle\Entity\GatewayConfig;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 */
class PaymentMethodForm extends AbstractForm
{
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


