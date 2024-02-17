<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class CustomerGroupCodeChoiceType extends AbstractType
{
    public function __construct( private RepositoryInterface $customerGroupRepository )
    {
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder->addModelTransformer( new ReversedTransformer(
            new ResourceToIdentifierTransformer( $this->customerGroupRepository, 'code' )
        ));
    }
    
    public function getParent(): string
    {
        return CustomerGroupChoiceType::class;
    }
    
    public function getBlockPrefix(): string
    {
        return 'vs_payment_customer_group_code_choice';
    }
}
