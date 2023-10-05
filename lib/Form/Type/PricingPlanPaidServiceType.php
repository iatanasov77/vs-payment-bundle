<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

final class PricingPlanPaidServiceType extends AbstractType
{
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
    
    public function getName()
    {
        return 'PricingPlanPaidService';
    }
}