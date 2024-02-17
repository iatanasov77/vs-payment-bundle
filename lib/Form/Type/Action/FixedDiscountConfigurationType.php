<?php namespace Vankosoft\PaymentBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;

final class FixedDiscountConfigurationType extends AbstractType
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
            ->add( 'amount', MoneyType::class, [
                'label'                 => 'vs_payment.form.promotion_action.fixed_discount_configuration_amount',
                'translation_domain'    => 'VSPaymentBundle',
                'constraints'           => [
                    new NotBlank( ['groups' => ['sylius']] ),
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                ],
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
        return 'vs_payment_promotion_action_fixed_discount_configuration';
    }
}