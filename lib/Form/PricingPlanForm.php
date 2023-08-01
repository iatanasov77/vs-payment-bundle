<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;
use Vankosoft\UsersSubscriptionsBundle\Model\PayedServiceSubscriptionPeriod;

class PricingPlanForm extends AbstractForm
{
    /** @var RequestStack */
    protected $requestStack;
    
    /** @var string */
    protected $categoryClass;
    
    /** @var string */
    protected $paidServicePeriodClass;
    
    public function __construct(
        RequestStack $requestStack,
        string $dataClass,
        string $categoryClass,
        string $paidServicePeriodClass
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack             = $requestStack;
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
                'choices'               => \array_flip( \Vankosoft\ApplicationBundle\Component\I18N::LanguagesAvailable() ),
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
                'multiple'              => true,
                'required'              => true,
                'placeholder'           => 'vs_payment.form.category_placeholder',
                'class'                 => $this->categoryClass,
                'choice_label'          => 'name',
            ])
            
            ->add( 'category_taxon', ChoiceType::class, [
                'label'                 => 'vs_payment.form.categories',
                'translation_domain'    => 'VSPaymentBundle',
                'multiple'              => true,
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
            
            ->add( 'paidServicePeriod', EntityType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.paid_service_period',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->paidServicePeriodClass,
                'choice_label'          => 'title',
                'group_by'              => function ( PayedServiceSubscriptionPeriod $paidServicePeriod ): string {
                    return $paidServicePeriod ? $paidServicePeriod->getPayedService()->getTitle() : 'Undefined Group';
                },
                'required'              => true,
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