<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

final class PromotionCouponForm extends AbstractForm
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->add( 'promotion', HiddenType::class, ['data' => $options['promotionId']] )
            
            ->add( 'code', TextType::class, [
                'label'                 => 'vs_payment.form.code',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
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
    
    public function configureOptions( OptionsResolver $resolver ) : void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                'promotionId'       => null,
            ])
            
            ->setDefined([
                'promotionId',
            ])
            
            //->setAllowedTypes( 'promotionId', 'int' )
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_coupon';
    }
}