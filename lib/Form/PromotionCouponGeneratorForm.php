<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class PromotionCouponGeneratorForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'amount', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.amount',
                'translation_domain'    => 'VSPaymentBundle',
                'mapped'                => false,
            ])
            
            ->add( 'prefix', TextType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.prefix',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
                'mapped'                => false,
            ])
            
            ->add( 'codeLength', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.code_length',
                'translation_domain'    => 'VSPaymentBundle',
                'mapped'                => false,
            ])
            
            ->add( 'suffix', TextType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.suffix',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
                'mapped'                => false,
            ])
            
            ->add( 'usageLimit', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.usage_limit',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
                'mapped'                => false,
            ])
            
            ->add( 'expiresAt', DateType::class, [
                'label'                 => 'vs_payment.form.promotion_coupon_generator.expires_at',
                'translation_domain'    => 'VSPaymentBundle',
                'widget'                => 'single_text',
                'required'              => false,
                'mapped'                => false,
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
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_coupon_generator';
    }
}
