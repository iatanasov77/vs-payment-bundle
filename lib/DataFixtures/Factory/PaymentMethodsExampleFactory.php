<?php namespace Vankosoft\PaymentBundle\DataFixtures\Factory;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\AbstractExampleFactory;
use Vankosoft\ApplicationInstalatorBundle\DataFixtures\Factory\ExampleFactoryInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\PaymentMethodInterface;

final class PaymentMethodsExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var FactoryInterface */
    private $paymentMethodsFactory;
    
    /** @var OptionsResolver */
    private $optionsResolver;
    
    /** @var RepositoryInterface */
    private $gatewayConfigsRepository;
    
    public function __construct(
        FactoryInterface $paymentMethodsFactory,
        RepositoryInterface $gatewayConfigsRepository
    ) {
        $this->paymentMethodsFactory    = $paymentMethodsFactory;
        
        $this->optionsResolver          = new OptionsResolver();
        $this->configureOptions( $this->optionsResolver );
        
        $this->gatewayConfigsRepository = $gatewayConfigsRepository;
    }
    
    public function create( array $options = [] ): PaymentMethodInterface
    {
        $options        = $this->optionsResolver->resolve( $options );
        
        $gatewayConfig  = $this->gatewayConfigsRepository->findOneBy( ['gatewayName' => $options['gateway']] );
        
        $entity         = $this->paymentMethodsFactory->createNew();
        $entity->setGateway( $gatewayConfig );
        $entity->setName( $options['name'] );
        $entity->setActive( $options['enabled'] );
        
        return $entity;
    }
    
    protected function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver
            ->setDefault( 'gateway', null )
            ->setAllowedTypes( 'gateway', ['string'] )
            
            ->setDefault( 'name', null )
            ->setAllowedTypes( 'name', ['string'] )
            
            ->setDefault( 'enabled', null )
            ->setAllowedTypes( 'enabled', ['bool'] )
        ;
    }
}
