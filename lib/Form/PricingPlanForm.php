<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            
            ->add( 'paidServicePeriod', EntityType::class, [
                'label'                 => 'vs_payment.form.pricing_plan.paid_service_period',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->paidServicePeriodClass,
                'choice_label'          => 'title',
                'group_by'              => function ( $pricingPlan ): string {
                    return $pricingPlan ? $pricingPlan->getPayedService()->getTitle() : 'Undefined Group';
                },
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.pricing_plan';
    }
}