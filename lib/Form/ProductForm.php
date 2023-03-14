<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\PaymentBundle\Model\Product;
use Vankosoft\PaymentBundle\Model\Interfaces\ProductInterface;

class ProductForm extends AbstractForm
{
    /** @var RequestStack */
    protected $requestStack;
    
    /** @var string */
    protected $categoryClass;
    
    /** @var string */
    protected $currencyClass;
    
    public function __construct(
        RequestStack $requestStack,
        string $dataClass,
        string $categoryClass,
        string $currencyClass
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack         = $requestStack;
        $this->categoryClass        = $categoryClass;
        $this->currencyClass        = $currencyClass;
    }

    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $entity         = $builder->getData();
        $currentLocale  = $entity->getTranslatableLocale() ?: $this->requestStack->getCurrentRequest()->getLocale();
        
        $builder
            ->add( 'locale', ChoiceType::class, [
                'label'                 => 'vs_cms.form.locale',
                'translation_domain'    => 'VSCmsBundle',
                'choices'               => \array_flip( \Vankosoft\ApplicationBundle\Component\I18N::LanguagesAvailable() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'enabled', CheckboxType::class, [
                'label'                 => 'vs_payment.form.active',
                'translation_domain'    => 'VSPaymentBundle',
            ])  
            
            ->add( 'category_taxon', ChoiceType::class, [
                'label'                 => 'vs_payment.form.categories',
                'translation_domain'    => 'VSPaymentBundle',
                'multiple'              => true,
                'required'              => false,   // Is Required but Used EasyUi
                'mapped'                => false,
                'placeholder'           => 'vs_payment.form.categories_placeholder',
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.name',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'description', TextType::class, [
                'label'                 => 'vs_payment.form.description',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'picture', FileType::class, [
                'mapped'                => false,
                'required'              => false,
                
                'label'                 => 'vs_application.form.picture',
                'translation_domain'    => 'VSApplicationBundle',
                
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/png',
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'vs_application.form.picture_invalid',
                    ])
                ],
            ])
            
            ->add( 'price', NumberType::class, [
                'label'                 => 'vs_payment.form.price',
                'required'              => true,
                'scale'                 => 2,
                'rounding_mode'         => $options['rounding_mode'],
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'currency', EntityType::class, [
                'label'                 => 'vs_payment.form.currency_label',
                'required'              => true,
                'class'                 => $this->currencyClass,
                'choice_label'          => 'code',
                'placeholder'           => 'vs_payment.form.currency_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
                'rounding_mode'     => \NumberFormatter::ROUND_HALFEVEN,
            ])
            
            ->setDefined([
                'product',
            ])
            
            ->setAllowedTypes( 'product', ProductInterface::class )
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.product';
    }
}

