<?php namespace Vankosoft\PaymentBundle\Form\Stripe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class CustomerForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'name', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.name',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.name'
                ],
            ])
            
            ->add( 'email', EmailType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.email',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.email'
                ],
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.stripe_subscription_customer';
    }
}