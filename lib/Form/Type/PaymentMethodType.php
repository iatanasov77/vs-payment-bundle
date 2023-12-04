<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PaymentMethodType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'paymentMethod', EntityType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.payment_method',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $options['paymentMethodClass'],
                'choice_label'          => 'name',
                'choice_attr'           => function ( $choice, string $key, mixed $value ) {
                    return ['data-paymentMethod' => $choice->getSlug()];
                },
                'expanded'              => true,
                'query_builder'         => function ( RepositoryInterface $er ) {
                    return $er->createQueryBuilder( 'pm' )->where( 'pm.enabled = 1' );
                }
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
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