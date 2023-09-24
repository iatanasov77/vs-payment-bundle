<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

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
        
            //->add( 'paymentMethod', EntityType::class, [
            ->add( 'paymentMethod', CollectionType::class, [
                'entry_type'            => EntityType::class,
                    
                'expanded'              => true,
                'class'                 => $this->paymentMethodClass,
                'query_builder' => function( EntityRepository $repository ) {
                    $qb = $repository->createQueryBuilder( 'pm' );
                    return $qb->where( $qb->expr()->eq( 'pm.enabled', '?1' ) )->setParameter( '1', '1' );
                },
                'choice_label'          => 'name',
                'label'                 => 'Payment Method',
                'translation_domain'    => 'VSUsersBundle',
            ])
            
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
