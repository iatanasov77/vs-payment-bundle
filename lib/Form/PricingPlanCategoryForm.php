<?php namespace Vankosoft\PaymentBundle\Form;

use Vankosoft\ApplicationBundle\Form\AbstractForm;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Vankosoft\ApplicationBundle\Component\I18N;

class PricingPlanCategoryForm extends AbstractForm
{
    protected $categoryClass;
    
    protected $repository;
    
    protected $requestStack;
    
    public function __construct( string $dataClass, EntityRepository $repository, RequestStack $requestStack )
    {
        parent::__construct( $dataClass );
        
        $this->categoryClass    = $dataClass;
        $this->repository       = $repository;
        $this->requestStack     = $requestStack;
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
                'choices'               => \array_flip( I18N::LanguagesAvailable() ),
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
                'query_builder'         => function ( EntityRepository $er ) use ( $category )
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