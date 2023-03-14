<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

/**
 * Credit Card Form Type for PayPal Pro Direct Payments
 */
class GatewayConfigType extends AbstractType
{
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add('factory', HiddenType::class)
        ;
        
        if( isset( $options['data']['sandbox'] ) ) {
            $builder->add( 'sandbox', HiddenType::class );
        }
        
        foreach( $options['data'] as $optKey => $optVal ) {
            if( in_array( $optKey, ['factory', 'sandbox'] ) )
                continue;
            
            //$value  = in_array( $optKey, $options['values'] );
            $builder->add( $optKey, TextType::class, [ 'attr' => ['size'=>100] ] );
        }
    }
    
    public function getName()
    {
        return 'config';
    }
}



