<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Customer\Model\CustomerGroupInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerGroupForm extends AbstractForm
{
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository
    ) {
        parent::__construct( $dataClass );
        
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