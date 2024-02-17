<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

use Vankosoft\ApplicationBundle\Form\Type\TaxonAutocompleteChoiceType;

final class HasTaxonConfigurationType extends AbstractType
{
    /** @var DataTransformerInterface */
    private $taxonsToCodesTransformer;
    
    public function __construct( DataTransformerInterface $taxonsToCodesTransformer )
    {
        $this->taxonsToCodesTransformer = $taxonsToCodesTransformer;
    }

    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'taxons', TaxonAutocompleteChoiceType::class, [
                'label'     => 'sylius.form.promotion_rule.has_taxon.taxons',
                'multiple'  => true,
            ])
        ;

        $builder->get( 'taxons' )->addModelTransformer( $this->taxonsToCodesTransformer );
    }

    public function getBlockPrefix(): string
    {
        return 'vs_payment_promotion_rule_has_taxon_configuration';
    }
}
