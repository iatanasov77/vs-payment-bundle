<?php namespace Vankosoft\PaymentBundle\Form\Stripe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CouponForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'duration', ChoiceType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.coupon_duration_type',
                'translation_domain' => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.template.payum_stripe_objects.coupon_duration_type_placeholder',
                'choices'               => \array_flip([
                    'forever'   => 'Forever',   // Applies to all charges from a subscription with this coupon applied.
                    'once'      => 'Once',      // Applies to the first charge from a subscription with this coupon applied.
                    'repeating' => 'Repeating', // Applies to charges in the first duration_in_months months from a subscription with this coupon applied.
                ]),
            ])
            
            ->add( 'duration_in_months', NumberType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.coupon_duration_months',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.coupon_duration_months'
                ],
                'required'  => false,
            ])
            
            ->add( 'percent_off', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.coupon_percent_off',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.coupon_percent_off'
                ],
                'required'  => false,
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
        return 'vs_payment.stripe_coupon';
    }
}