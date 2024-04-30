<?php namespace Vankosoft\PaymentBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Type;
use Vankosoft\PaymentBundle\Form\Type\PromotionFilterCollectionType;

final class UnitPercentageDiscountConfigurationType extends AbstractType
{
    /** @var string */
    private $baseCurrency;
    
    public function __construct( string $baseCurrency )
    {
        $this->baseCurrency = $baseCurrency;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'percentage', PercentType::class, [
                'label'                 => 'vs_payment.form.promotion_action.percentage_discount_configuration_percentage',
                'translation_domain'    => 'VSPaymentBundle',
                'html5'                 => true,
                'scale'                 => 2,
            ])
            ->add( 'filters', PromotionFilterCollectionType::class, [
                'label'                 => 'vs_payment.form.promotion_action.filters',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
                'currency'              => $options['currency'],
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setRequired( 'currency' )
            ->setAllowedTypes( 'currency', 'string' )
            
            ->setDefaults([
                'currency'  => $this->baseCurrency,
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_action_unit_percentage_discount_configuration';
    }
}