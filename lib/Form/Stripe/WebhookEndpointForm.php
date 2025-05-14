<?php namespace Vankosoft\PaymentBundle\Form\Stripe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

class WebhookEndpointForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'id', HiddenType::class, ['data' => $options['endpointId']] )
            ->add( 'selectedEvents', HiddenType::class, ['data' => \json_encode( $options['endpointEvents'] )] )
            
            ->add( 'enabled', CheckboxType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.active',
                'translation_domain'    => 'VSPaymentBundle',
                'data'                  => $options['endpointStatus'] == 'enabled',
            ])
        
            ->add( 'enabled_events', ChoiceType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.enabled_events',
                'translation_domain'    => 'VSPaymentBundle',
                'choices'               => \array_flip( StripeApi::STRIPE_EVENTS ),
                'multiple'              => true,
                'required'              => false,
            ])
        
            ->add( 'url', TextType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.url',
                'attr'                  => ['placeholder' => 'vs_payment.template.payum_stripe_objects.url'],
                'translation_domain'    => 'VSPaymentBundle',
                'data'                  => $options['endpointUrl']
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                
                'endpointId'        => null,
                'endpointStatus'    => 'enabled',
                'endpointEvents'    => [],
                'endpointUrl'       => '',
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.stripe_subscription_webhook_endpoint';
    }
}