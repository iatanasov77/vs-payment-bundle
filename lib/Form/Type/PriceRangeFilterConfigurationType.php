<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

final class PriceRangeFilterConfigurationType extends AbstractType
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
            ->add( 'min', MoneyType::class, [
                'required'      => false,
                'constraints'   => [
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                ],
                'currency'      => $options['currency'],
            ])
            ->add( 'max', MoneyType::class, [
                'required'      => false,
                'constraints'   => [
                    new Type( ['type' => 'numeric', 'groups' => ['sylius']] ),
                ],
                'currency'      => $options['currency'],
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
        return 'sylius_promotion_action_filter_price_range_configuration';
    }
}