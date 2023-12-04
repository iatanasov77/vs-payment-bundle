<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Vankosoft\PaymentBundle\Form\Type\PaymentMethodType;

use Doctrine\ORM\EntityRepository;

class PaymentForm extends AbstractType
{
    /** @var string */
    protected $paymentMethodClass;
    
    public function __construct(
        string $paymentMethodClass
    ) {
        $this->paymentMethodClass   = $paymentMethodClass;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'paymentDescription', HiddenType::class )
        
            ->add( 'paymentMethod', PaymentMethodType::class, [
                'paymentMethodClass'    => $this->paymentMethodClass
            ] )
            
            ->add( 'btnContinue', SubmitType::class, [
                'label' => 'Continue',
                'translation_domain' => 'VSUsersBundle'
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ) : void
    {
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.payment';
    }
}
