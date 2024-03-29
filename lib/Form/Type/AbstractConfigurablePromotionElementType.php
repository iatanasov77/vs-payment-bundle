<?php namespace Vankosoft\PaymentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Sylius\Bundle\ResourceBundle\Form\Registry\FormTypeRegistryInterface;

use Sylius\Component\Promotion\Model\ConfigurablePromotionElementInterface;

abstract class AbstractConfigurablePromotionElementType extends AbstractResourceType
{
    private FormTypeRegistryInterface $formTypeRegistry;
    
    public function __construct( string $dataClass, array $validationGroups, FormTypeRegistryInterface $formTypeRegistry )
    {
        parent::__construct( $dataClass, $validationGroups );
        
        $this->formTypeRegistry = $formTypeRegistry;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        parent::buildForm( $builder, $options );
        
        $builder
            ->addEventListener( FormEvents::PRE_SET_DATA, function ( FormEvent $event ): void {
                $type = $this->getRegistryIdentifier( $event->getForm(), $event->getData() );
                if ( null === $type ) {
                    return;
                }
                
                $configurationType  = $this->formTypeRegistry->get( $type, 'default' );
                if ( ! $configurationType ) {
                    return;
                }
                
                $this->addConfigurationFields( $event->getForm(), $configurationType );
            })
            
            ->addEventListener( FormEvents::POST_SET_DATA, function ( FormEvent $event ) {
                $type = $this->getRegistryIdentifier( $event->getForm(), $event->getData() );
                if ( null === $type ) {
                    return;
                }
                
                $event->getForm()->get( 'type' )->setData( $type );
            })
            
            ->addEventListener( FormEvents::PRE_SUBMIT, function ( FormEvent $event ): void {
                $data = $event->getData();
                
                if ( ! isset( $data['type'] ) ) {
                    return;
                }
                
                $configurationType  = $this->formTypeRegistry->get( $data['type'], 'default' );
                if ( ! $configurationType ) {
                    return;
                }
                
                $this->addConfigurationFields( $event->getForm(), $configurationType );
            })
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver
            ->setDefault( 'configuration_type', null )
            ->setAllowedTypes( 'configuration_type', ['string', 'null'] )
        ;
    }
    
    protected function addConfigurationFields( FormInterface $form, string $configurationType ): void
    {
        $form->add( 'configuration', $configurationType, [
            'label' => false,
        ]);
    }
    
    protected function getRegistryIdentifier( FormInterface $form, $data = null ): ?string
    {
        if ( $data instanceof ConfigurablePromotionElementInterface && null !== $data->getType() ) {
            return $data->getType();
        }
        
        return $form->getConfig()->getOption( 'configuration_type' );
    }
}