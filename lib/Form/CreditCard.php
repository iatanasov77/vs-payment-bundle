<?php

namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 * 
 * THIS IS OLD BETTER TO USE NEW CreditCardForm 
 */
class CreditCard extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {        
        $builder
            ->add('acct', TextType::class, array('label' => 'form.credit_card.acct', 'translation_domain' => 'IAPaymentBundle'))
            ->add('exp_date', DateType::class, array(
                'label' => 'form.credit_card.expire_date', 
                'translation_domain' => 'VSPaymentBundle',
                'widget' => 'choice',
                'format' =>'MM-yyyy  d',
                'years' => range(date('Y'), date('Y')+12),
                'days' => array(1)
            ))
            ->add('cvv', TextType::class, array('label' => 'form.credit_card.cvv2', 'translation_domain' => 'IAPaymentBundle'))
            ->add( 'amt', HiddenType::class, [
                'data' => '1.00',
            ])
            ->add( 'currency', HiddenType::class, [
                'data' => 'USD',
            ])
            ->add( 'btnPay', SubmitType::class, array( 'label' => 'Pay' ) )
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
//        $resolver->setDefaults(array(
//            'data_class' => 'IA\Bundle\UsersBundle\Entity\User'
//        ));
    }

    public function getName()
    {
        return 'vs_payment_credit_card';
    }
}

