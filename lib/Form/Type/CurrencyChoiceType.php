<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CurrencyChoiceType extends AbstractType
{
    /** @var RepositoryInterface */
    private $currencyRepository;
    
    public function __construct( RepositoryInterface $currencyRepository )
    {
        $this->currencyRepository   = $currencyRepository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        if ( $options['multiple'] ) {
            $builder->addModelTransformer( new CollectionToArrayTransformer() );
        }
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'choices'                   => fn ( Options $options ): array => $this->currencyRepository->findAll(),
            'choice_value'              => 'code',
            'choice_label'              => 'name',
            'choice_translation_domain' => false,
        ]);
    }
    
    public function getParent(): string
    {
        return ChoiceType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_currency_choice';
    }
}