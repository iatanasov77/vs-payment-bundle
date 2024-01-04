<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\HttpFoundation\RequestStack;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Vankosoft\PaymentBundle\Model\Interfaces\CouponInterface;

class CouponForm extends AbstractForm
{
    /** @var string */
    protected $currencyClass;
    
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository,
        string $currencyClass
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack         = $requestStack;
        $this->localesRepository    = $localesRepository;
        
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
                'choices'               => \array_flip( $this->fillLocaleChoices() ),
                'data'                  => $currentLocale,
                'mapped'                => false,
            ])
        
            ->add( 'enabled', CheckboxType::class, [
                'required'              => false,
                'label'                 => 'vs_payment.form.coupon.valid',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'code', TextType::class, [
                'label'                 => 'vs_payment.form.coupon.coupon_code',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => true,
            ])
            
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.coupon.coupon_name',
                'translation_domain'    => 'VSPaymentBundle',
                'required'              => false,
            ])
            
            ->add( 'amountOff', NumberType::class, [
                'label'                 => 'vs_payment.form.coupon.amount_off',
                'translation_domain'    => 'VSPaymentBundle',
                'scale'                 => 2,
                'rounding_mode'         => $options['rounding_mode'],
                'required'              => false,
            ])
            
            ->add( 'currency', EntityType::class, [
                'label'                 => 'vs_payment.form.currency_label',
                'required'              => false,
                'class'                 => $this->currencyClass,
                'choice_label'          => 'code',
                'placeholder'           => 'vs_payment.form.currency_placeholder',
                'translation_domain'    => 'VSPaymentBundle',
            ])
            
            ->add( 'percentOff', NumberType::class, [
                'label'                 => 'vs_payment.form.coupon.percent_off',
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
            ])
            
            ->setDefined([
                'coupon',
            ])
            
            ->setAllowedTypes( 'coupon', CouponInterface::class )
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.coupon';
    }
}