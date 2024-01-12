<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CouponCodeForm extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'couponCode', TextType::class, [
                'label' => 'vs_payment.form.coupon.coupon_code'
            ])
        ;
    }
    
    /**
     * {@inheritDoc}
     */
    public function configureOptions( OptionsResolver $resolver )
    {
        $resolver
            ->setDefaults([
                'data_class'            => CouponCodeModel::class,
                'validation_groups'     => ['VSPayment'],
                'label'                 => false,
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }
}