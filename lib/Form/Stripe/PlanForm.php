<?php namespace Vankosoft\PaymentBundle\Form\Stripe;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;

class PlanForm extends AbstractType
{
    /** @var string */
    private $currencyClass;
    
    public function __construct(
        string $currencyClass
    ) {
        $this->currencyClass    = $currencyClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'id', TextType::class, [
                'label' => 'ID',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'ID'
                ],
            ])
            
            ->add( 'amount', NumberType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.amount',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.amount'
                ],
                'scale'                 => 2,
                'rounding_mode'         => \NumberFormatter::ROUND_HALFUP,
            ])
            
            ->add( 'currency', EntityType::class, [
                'label' => 'vs_payment.form.currency_label',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->currencyClass,
                'placeholder'           => 'vs_payment.form.currency_placeholder',
                'choice_label'          => 'name',
                'choice_value'          => function ( ?CurrencyInterface $entity ): string {
                    return $entity ? $entity->getCode() : '';
                },
            ])
            
            ->add( 'interval', ChoiceType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.interval',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.template.payum_stripe_objects.interval_placeholder',
                'choices'               => \array_flip([
                    'day'   => 'Day',
                    'week'  => 'Week',
                    'month' => 'Month',
                    'year'  => 'Year',
                ]),
            ])
            
            ->add( 'productName', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.product_name',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.product_name'
                ],
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.stripe_subscription_plan';
    }
}