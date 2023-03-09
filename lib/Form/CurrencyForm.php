<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType as SymfonyCurrencyType;
use Symfony\Component\Form\FormBuilderInterface;
use Vankosoft\ApplicationBundle\Form\AbstractForm;

final class CurrencyForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->addEventSubscriber( new AddCodeFormSubscriber( SymfonyCurrencyType::class, [
                'label'                 => 'vs_payment.form.currency.code',
                'translation_domain'    => 'VSPaymentBundle',
            ]))
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_currency';
    }
}