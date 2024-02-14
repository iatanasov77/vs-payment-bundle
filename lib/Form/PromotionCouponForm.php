<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionCouponForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'usageLimit', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion.usage_limit',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'expiresAt', DateType::class, [
                'label'                 => 'vs_payment.form.promotion.expires_at',
                'translation_domain'    => 'VSPaymentBundle',
                'widget'                => 'single_text',
                'placeholder'           => ['year' => '-', 'month' => '-', 'day' => '-'],
                'required'              => false,
            ])
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment.promotion_coupon';
    }
}