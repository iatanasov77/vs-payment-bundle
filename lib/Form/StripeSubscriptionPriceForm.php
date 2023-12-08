<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class StripeSubscriptionPriceForm extends AbstractType
{
    /** @var StripeApi */
    private $stripeApi;
    
    /** @var string */
    private $currencyClass;
    
    /** @var string */
    private $pricingPlanClass;
    
    public function __construct(
        StripeApi $stripeApi,
        string $currencyClass,
        string $pricingPlanClass
    ) {
        $this->stripeApi        = $stripeApi;
        $this->currencyClass    = $currencyClass;
        $this->pricingPlanClass = $pricingPlanClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            /*
            ->add( 'id', TextType::class, [
                'label' => 'ID',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'ID'
                ],
            ])
            */
        
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
            
            ->add( 'intervalCount', NumberType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.interval_count',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.interval_count'
                ],
            ])
            
            ->add( 'product', ChoiceType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.product',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.template.payum_stripe_objects.product_placeholder',
                'choices'               => \array_flip( $this->stripeApi->getProductPairs() ),
            ])
            
            ->add( 'pricingPlan', EntityType::class, [
                'label'                 => 'vs_payment.template.payum_stripe_objects.pricing_plan',
                'translation_domain'    => 'VSPaymentBundle',
                'placeholder'           => 'vs_payment.template.payum_stripe_objects.pricing_plan_placeholder',
                'class'                 => $this->pricingPlanClass,
                'choice_label'          => 'title',
                'group_by'              => function ( PricingPlanInterface $pricingPlan ): string {
                    return $pricingPlan ? $pricingPlan->getCategory()->getName() : 'Undefined Category';
                },
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label' => 'vs_application.form.save',
                'translation_domain' => 'VSApplicationBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
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
        return 'vs_payment.stripe_subscription_price';
    }
}