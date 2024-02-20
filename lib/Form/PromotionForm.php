<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vankosoft\PaymentBundle\Form\Type\PromotionRuleCollectionType;
use Vankosoft\PaymentBundle\Form\Type\PromotionActionCollectionType;

class PromotionForm extends AbstractForm
{
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack             = $requestStack;
        $this->localesRepository        = $localesRepository;
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
        
            ->add( 'code', TextType::class, [
                'label'                 => 'vs_payment.form.code',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.name',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'description', TextareaType::class, [
                'label'                 => 'vs_payment.form.description',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'exclusive', CheckboxType::class, [
                'label'                 => 'vs_payment.form.promotion.exclusive',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'usageLimit', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion.usage_limit',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'startsAt', DateTimeType::class, [
                'label'                 => 'vs_payment.form.promotion.starts_at',
                'translation_domain'    => 'VSPaymentBundle',
                'date_widget'           => 'single_text',
                'time_widget'           => 'single_text',
                'required'              => false,
            ])
            
            ->add( 'endsAt', DateTimeType::class, [
                'label'                 => 'vs_payment.form.promotion.ends_at',
                'translation_domain'    => 'VSPaymentBundle',
                'date_widget'           => 'single_text',
                'time_widget'           => 'single_text',
                'required'              => false,
            ])
            
            ->add( 'priority', IntegerType::class, [
                'label'                 => 'vs_payment.form.promotion.priority',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'couponBased', CheckboxType::class, [
                'label'                 => 'vs_payment.form.promotion.coupon_based',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'rules', PromotionRuleCollectionType::class, [
                'label'                 => 'vs_payment.form.promotion.rules',
                'translation_domain'    => 'VSPaymentBundle',
                'button_add_label'      => 'sylius.form.promotion.add_rule',
            ])
            
            ->add( 'actions', PromotionActionCollectionType::class, [
                'label'                 => 'vs_payment.form.promotion.actions',
                'translation_domain'    => 'VSPaymentBundle',
                'button_add_label'      => 'sylius.form.promotion.add_action',
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
        ;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion';
    }
}