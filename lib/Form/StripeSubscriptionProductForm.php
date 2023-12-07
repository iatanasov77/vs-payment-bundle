<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StripeSubscriptionProductForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            /*
            ->add( 'id', TextType::class, [
                'label' => 'ID',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'ID'
                ],
            ])
            */
        
            ->add( 'name', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.name',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.name'
                ],
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label' => 'vs_application.form.save',
                'translation_domain' => 'VSApplicationBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
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
        return 'vs_payment.stripe_subscription_product';
    }
}