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
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'captureUrl', HiddenType::class, ['empty_data' => $options['captureUrl']] )
            
            ->add( 'name', TextType::class, [
                'label' => 'Name',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'Enter your name'
                ],
            ])
            
            ->add( 'number', NumberType::class, [
                'label' => 'Credit Card Number',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => '0000 0000 0000 0000'
                ],
            ])
            
            ->add( 'ccmonth', ChoiceType::class, [
                'choices'               => array_combine( range( 1, 12 ), range( 1, 12 ) ),
                'mapped'                => false,
                'label'                 => 'Month',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'ccyear', ChoiceType::class, [
                'choices'               => array_combine( range( 2014, 2025 ), range( 2014, 2025 ) ),
                'mapped'                => false,
                'label'                 => 'Year',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'cvv', TextType::class, [
                'label' => 'CVV/CVC',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => '123'
                ],
            ])
            
            ->add( 'btnReset', ResetType::class, [
                'label' => 'Reset',
                'translation_domain' => 'VSPaymentBundle'
            ])
            
            ->add( 'btnContinue', SubmitType::class, [
                'label' => 'Continue',
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
                'captureUrl'        => null,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.credit_card';
    }
}
