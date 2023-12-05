<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class PricingPlanCategoryForm extends AbstractForm
{
    /** @var string */
    protected $categoryClass;
    
    /** @var RepositoryInterface */
    protected $repository;
    
    public function __construct(
        string $dataClass,
        RequestStack $requestStack,
        RepositoryInterface $localesRepository,
        RepositoryInterface $repository
    ) {
        parent::__construct( $dataClass );
        
        $this->requestStack         = $requestStack;
        $this->localesRepository    = $localesRepository;
        
        $this->categoryClass        = $dataClass;
        $this->repository           = $repository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $category   = $options['data'];
        
        $builder
            ->setMethod( $category && $category->getId() ? 'PUT' : 'POST' )
            
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
            ] )
            
            ->add( 'parent', EntityType::class, [
                'label'                 => 'vs_payment.form.parent_category',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->categoryClass,
                'query_builder'         => function ( RepositoryInterface $er ) use ( $category )
                {
                    $qb = $er->createQueryBuilder( 'pc' );
                    if  ( $category && $category->getId() ) {
                        $qb->where( 'pc.id != :id' )->setParameter( 'id', $category->getId() );
                    }
                    
                    return $qb;
            },
            'choice_label'  => 'name',
            
            'required'      => false,
            'placeholder'   => 'vs_payment.form.parent_category_placeholder',
            ])
        ;
    }
    
    public function getName()
    {
        return 'vs_payment.pricing_plan_category';
    }
}