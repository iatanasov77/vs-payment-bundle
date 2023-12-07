<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\PaymentBundle\Model\Interfaces\CurrencyInterface;

class StripeSubscriptionPlanForm extends AbstractType
{
    /** @var string */
    private $currencyClass;
    
    public function __construct(
        string $currencyClass
    ) {
        $this->currencyClass    = $currencyClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'id', TextType::class, [
                'label' => 'ID',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'ID'
                ],
            ])
            
            ->add( 'amount', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.amount',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.amount'
                ],
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
            
            ->add( 'interval', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.interval',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.interval'
                ],
            ])
            
            ->add( 'productName', TextType::class, [
                'label' => 'vs_payment.template.payum_stripe_objects.product_name',
                'translation_domain' => 'VSPaymentBundle',
                'attr'  => [
                    'placeholder' => 'vs_payment.template.payum_stripe_objects.product_name'
                ],
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
        return 'vs_payment.stripe_subscription_plan';
    }
}