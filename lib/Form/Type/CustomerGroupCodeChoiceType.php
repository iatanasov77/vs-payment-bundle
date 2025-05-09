<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

final class CustomerGroupCodeChoiceType extends AbstractType
{
    /** @var RepositoryInterface */
    private $customerGroupRepository;
    
    public function __construct( RepositoryInterface $customerGroupRepository )
    {
        $this->customerGroupRepository  = $customerGroupRepository;
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
