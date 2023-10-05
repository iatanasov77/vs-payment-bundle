<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use Vankosoft\PaymentBundle\Form\Type\CurrencyChoiceType;
use Vankosoft\PaymentBundle\Form\Type\PricingPlanPaidServiceType;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\PayedServiceSubscriptionPeriod;

class PricingPlanForm extends AbstractForm
{
    /** @var string */
    protected $categoryClass;
    
    /** @var string */
    protected $paidServicePeriodClass;
    
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository,
        string $categoryClass,
        string $paidServicePeriodClass
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack             = $requestStack;
        $this->localesRepository        = $localesRepository;
        
        $this->categoryClass            = $categoryClass;
        $this->paidServicePeriodClass   = $paidServicePeriodClass;
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
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
            
            ->add( 'enabled', CheckboxType::class, [
                'label'                 => 'vs_payment.form.active',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'category', EntityType::class, [
                'label'                 => 'vs_payment.form.category',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => true,
                'placeholder'           => 'vs_payment.form.category_placeholder',
                'class'                 => $this->categoryClass,
                'choice_label'          => 'name',
            ])
            
            ->add( 'category_taxon', ChoiceType::class, [
                'label'                 => 'vs_payment.form.categories',
                'translation_domain'    => 'VSPaymentBundle',
                'multiple'              => false,
                'required'              => false,   // Is Required but Used EasyUi
                'mapped'                => false,
                'placeholder'           => 'vs_payment.form.categories_placeholder',
            ])
            
            ->add( 'title', TextType::class, [
                'label'                 => 'vs_payment.form.title',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'description', CKEditorType::class, [
                'label'                 => 'vs_payment.form.description',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
                'config'                => [
                    'uiColor'                           => $options['ckeditor_uiColor'],
                    'extraAllowedContent'               => $options['ckeditor_extraAllowedContent'],
                    
                    'toolbar'                           => $options['ckeditor_toolbar'],
                    'extraPlugins'                      => array_map( 'trim', explode( ',', $options['ckeditor_extraPlugins'] ) ),
                    'removeButtons'                     => $options['ckeditor_removeButtons'],
                ],
            ])
            
            ->add( 'premium', CheckboxType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.premium',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'discount', NumberType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.discount',
                'translation_domain'    => 'VSPaymentBundle',
                'scale'                 => 2,
                'rounding_mode'         => $options['rounding_mode'],
                'required'              => false,
            ])
            
            ->add( 'price', NumberType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.price',
                'translation_domain'    => 'VSPaymentBundle',
                'scale'                 => 2,
                'rounding_mode'         => $options['rounding_mode'],
                'required'              => true,
            ])
            
            ->add( 'currency', CurrencyChoiceType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.currency',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => true,
            ])
            
            ->add( 'paidServices', CollectionType::class, [
                'entry_type'   => PricingPlanPaidServiceType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'prototype'    => true,
                'by_reference' => false
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
                
                // CKEditor Options
                'ckeditor_uiColor'              => '#ffffff',
                'ckeditor_extraAllowedContent'  => '*[*]{*}(*)',
                
                'ckeditor_toolbar'              => 'full',
                'ckeditor_extraPlugins'         => '',
                'ckeditor_removeButtons'        => '',
            ])
            
            ->setDefined([
                'pricing_plan',
                
                // CKEditor Options
                'ckeditor_uiColor',
                'ckeditor_extraAllowedContent',
                'ckeditor_toolbar',
                'ckeditor_extraPlugins',
                'ckeditor_removeButtons',
            ])
            
            ->setAllowedTypes( 'pricing_plan', PricingPlanInterface::class )
            
            // CKEditor Options
            ->setAllowedTypes( 'ckeditor_uiColor', 'string' )
            ->setAllowedTypes( 'ckeditor_extraAllowedContent', 'string' )
            ->setAllowedTypes( 'ckeditor_toolbar', 'string' )
            ->setAllowedTypes( 'ckeditor_extraPlugins', 'string' )
            ->setAllowedTypes( 'ckeditor_removeButtons', 'string' )
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.pricing_plan';
    }
}