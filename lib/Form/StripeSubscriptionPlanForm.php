<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class StripeSubscriptionPlanForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'name', TextType::class, [
                'label' => 'vs_payment.form.credit_card.holder_name_label',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.credit_card.holder_name_placeholder'
                ],
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label' => 'vs_payment.form.credit_card.continue',
                'translation_domain' => 'VSPaymentBundle'
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
        return 'vs_payment.stripe_subscription_plan';
    }
}