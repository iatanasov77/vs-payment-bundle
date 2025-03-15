<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\PaymentBundle\Component\Payment\Payment;

class PaymentMethodType extends AbstractType
{
    /** @var Payment */
    private $vsPayment;
    
    public function __construct( Payment $vsPayment )
    {
        $this->vsPayment    = $vsPayment;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'setRecurringPayments', CheckboxType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.select_pricing_plan.set_recurring_payments',
                'translation_domain'    => 'VSPaymentBundle',
            ] )
        
            ->add( 'paymentMethod', EntityType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.payment_method',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $options['paymentMethodClass'],
                'choice_label'          => 'name',
                'choice_attr'           => function ( $choice, string $key, mixed $value ) {
                    return [
                        'data-paymentMethod'    => $choice->getSlug(),
                        'data-supportRecurring' => (string)$this->vsPayment->isGatewaySupportRecurring( $choice->getGateway() ),
                    ];
                },
                'expanded'              => true,
                'query_builder'         => function ( RepositoryInterface $er ) {
                    return $er->createQueryBuilder( 'pm' )->where( 'pm.enabled = 1' );
                }
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefined([
                'paymentMethodClass',
            ])
            
            ->setAllowedTypes( 'paymentMethodClass', 'string' )
        ;
    }
    
    public function getName()
    {
        return 'paymentMethod';
    }
}