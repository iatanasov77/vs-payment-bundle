<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\UsersSubscriptionsBundle\Model\PayedServiceSubscriptionPeriod;

final class PricingPlanPaidServiceType extends AbstractType
{
    /** @var string */
    private  $paidServicePeriodClass;
    
    public function __construct(
        string $paidServicePeriodClass
    ) {
        $this->paidServicePeriodClass   = $paidServicePeriodClass;
    }
    
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'paidServicePeriod', EntityType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.paid_service_period',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->paidServicePeriodClass,
                'choice_label'          => 'title',
                'group_by'              => function ( PayedServiceSubscriptionPeriod $paidServicePeriod ): string {
                    return $paidServicePeriod ? $paidServicePeriod->getPayedService()->getTitle() : 'Undefined Group';
                },
                'required'              => true,
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver->setDefaults(array(
            'data_class' => $this->paidServicePeriodClass
        ));
    }
    
    public function getName()
    {
        return 'PricingPlanPaidService';
    }
}