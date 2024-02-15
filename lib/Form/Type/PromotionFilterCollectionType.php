<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\PaymentBundle\Form\Type\PriceRangeFilterConfigurationType;

final class PromotionFilterCollectionType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder->add( 'price_range_filter', PriceRangeFilterConfigurationType::class, [
            'label'                 => 'vs_payment.form.promotion.price_range',
            'translation_domain'    => 'VSPaymentBundle',
            'required'              => false,
            'currency'              => $options['currency'],
        ]);
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setRequired( 'currency' )
            ->setAllowedTypes( 'currency', 'string' )
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_filters';
    }
}