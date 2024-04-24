<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class CreditCardForm extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'captureUrl', HiddenType::class, ['empty_data' => $options['captureUrl']] )
            
            ->add( 'name', TextType::class, [
                'label' => 'vs_payment.form.credit_card.holder_name_label',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.credit_card.holder_name_placeholder'
                ],
            ])
            
            ->add( 'number', NumberType::class, [
                'label' => 'vs_payment.form.credit_card.card_number_label',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.credit_card.card_number_placeholder'
                ],
            ])
            
            ->add( 'ccmonth', ChoiceType::class, [
                'choices'               => array_combine( range( 1, 12 ), range( 1, 12 ) ),
                'mapped'                => false,
                'label'                 => 'vs_payment.form.credit_card.exp_date_month',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'ccyear', ChoiceType::class, [
                'choices'               => array_combine( range( 2014, 2025 ), range( 2014, 2025 ) ),
                'mapped'                => false,
                'label'                 => 'vs_payment.form.credit_card.exp_date_year',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'cvv', TextType::class, [
                'label' => 'vs_payment.form.credit_card.cvv_cvc',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.credit_card.cvv_cvc_placeholder'
                ],
            ])
            
            ->add( 'btnReset', ResetType::class, [
                'label' => 'vs_payment.form.credit_card.reset',
                'translation_domain' => 'VSPaymentBundle'
            ])
            
            ->add( 'btnContinue', SubmitType::class, [
                'label' => 'vs_payment.form.credit_card.continue',
                'translation_domain' => 'VSPaymentBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                'captureUrl'        => null,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.credit_card';
    }
}
