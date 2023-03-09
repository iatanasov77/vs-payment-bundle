<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Form\Extension\Core\DataTransformer\NumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vankosoft\ApplicationBundle\Form\AbstractForm;

use Vankosoft\PaymentBundle\Form\Type\CurrencyChoiceType;

final class ExchangeRateForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'ratio', NumberType::class, [
                'label'                 => 'vs_payment.form.exchange_rate.ratio',
                'required'              => true,
                'invalid_message'       => 'vs_payment.validation.exchange_rate_ratio_invalid',
                'scale'                 => 5,
                'rounding_mode'         => $options['rounding_mode'],
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
        
        $builder->addEventListener( FormEvents::PRE_SET_DATA, function ( FormEvent $event ): void {
            /** @var ExchangeRateInterface $exchangeRate */
            $exchangeRate = $event->getData();
            $form = $event->getForm();
            
            $disabled = null !== $exchangeRate->getId();
            
            $form
                ->add( 'sourceCurrency', CurrencyChoiceType::class, [
                    'label'                 => 'vs_payment.form.exchange_rate.source_currency',
                    'required'              => true,
                    'empty_data'            => false,
                    'disabled'              => $disabled,
                    'translation_domain'    => 'VSPaymentBundle',
                ])
                ->add( 'targetCurrency', CurrencyChoiceType::class, [
                    'label'                 => 'vs_payment.form.exchange_rate.target_currency',
                    'required'              => true,
                    'empty_data'            => false,
                    'disabled'              => $disabled,
                    'translation_domain'    => 'VSPaymentBundle',
                ])
            ;
        });
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        /** @psalm-suppress DeprecatedConstant */
        $resolver->setDefault( 'rounding_mode', \NumberFormatter::ROUND_HALFEVEN );
    }
    
    public function getBlockPrefix()
    {
        return 'vs_exchange_rate';
    }
}