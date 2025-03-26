<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Vankosoft\PaymentBundle\Form\Type\ExchangeRateServiceOptionsType;

class ExchangeRateServiceForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {       
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'title', TextType::class, [
                'required'              => true,
                'label'                 => 'vs_payment.form.title',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'serviceId', TextType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.service_id',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'options', CollectionType::class, [
                'entry_type'    => ExchangeRateServiceOptionsType::class,
                'allow_add'     => true,
                'allow_delete'  => true,
                'prototype'     => true,
                'by_reference'  => false,
            ])
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
    }

    public function getName()
    {
        return 'vs_payment.exchange_rate_service';
    }
}


