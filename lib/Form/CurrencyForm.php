<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType as SymfonyCurrencyType;
use Symfony\Component\Form\FormBuilderInterface;

final class CurrencyForm extends AbstractResourceType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
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