<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomerGroupChoiceType extends AbstractType
{
    public function __construct( private RepositoryInterface $customerGroupRepository )
    {
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
            'choices'                   => fn ( Options $options ): array => $this->customerGroupRepository->findAll(),
            'choice_value'              => 'code',
            'choice_label'              => 'name',
            'choice_translation_domain' => false,
            'label'                     => 'sylius.form.customer.group',
        ]);
    }
    
    public function getParent(): string
    {
        return ChoiceType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_customer_group_choice';
    }
}
