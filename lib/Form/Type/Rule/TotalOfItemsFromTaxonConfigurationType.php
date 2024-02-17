<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\ApplicationBundle\Form\Type\TaxonAutocompleteChoiceType;

final class TotalOfItemsFromTaxonConfigurationType extends AbstractType
{
    /** @var RepositoryInterface */
    private $taxonRepository;
    
    public function __construct( RepositoryInterface $taxonRepository )
    {
        $this->taxonRepository  = $taxonRepository;
    }

    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'taxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.taxon',
            ])
            ->add( 'amount', MoneyType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.amount',
                'currency' => $options['currency'],
            ])
        ;

        $builder->get( 'taxon' )->addModelTransformer(
            new ReversedTransformer( new ResourceToIdentifierTransformer( $this->taxonRepository, 'code' ) ),
        );
    }

    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setRequired( 'currency' )
            ->setAllowedTypes( 'currency', 'string' )
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_total_of_items_from_taxon_configuration';
    }
}
