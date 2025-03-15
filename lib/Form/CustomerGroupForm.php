<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CustomerGroupForm extends AbstractForm
{
    /** @var string */
    private $customerClass;
    
    public function __construct(
        string $dataClass,
        string $customerClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository
    ) {
        parent::__construct( $dataClass );
        $this->customerClass        = $customerClass;
        
        $this->requestStack         = $requestStack;
        $this->localesRepository    = $localesRepository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $group  = $options['data'];
        
        $builder
            ->setMethod( $group && $group->getId() ? 'PUT' : 'POST' )
            
            ->add( 'currentLocale', ChoiceType::class, [
                'label'                 => 'vs_cms.form.locale',
                'translation_domain'    => 'VSCmsBundle',
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $this->requestStack->getCurrentRequest()->getLocale(),
                'mapped'                => false,
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.name',
                'translation_domain'    => 'VSPaymentBundle',
                'mapped'                => false,
            ])
            
            ->add( 'customers', EntityType::class, [
                'class'                 => $this->customerClass,
                'choice_label'          => 'customerChoiceLabel',
                'label'                 => 'vs_payment.form.customer_group.users',
                'placeholder'           => 'vs_payment.form.customer_group.users_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
                'multiple'              => true,
                //'mapped'                => false,
                'required'              => false,
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
            
            ->setDefined([
                'customer_group',
            ])
            
            ->setAllowedTypes( 'customer_group', CustomerGroupInterface::class )
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.customer_group';
    }
}