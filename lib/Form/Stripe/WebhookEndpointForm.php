<?php namespace Vankosoft\PaymentBundle\Form\Stripe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class WebhookEndpointForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'enabled_events', ChoiceType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.enabled_events',
                'translation_domain'    => 'VSPaymentBundle',
                'choices'               => \array_combine( StripeApi::STRIPE_EVENTS, StripeApi::STRIPE_EVENTS ),
                'multiple'              => true,
            ])
        
            ->add( 'url', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.url',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.url'
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
        return 'vs_payment.stripe_subscription_webhook_endpoint';
    }
}