<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Payum\Core\Bridge\Symfony\Builder\GatewayFactoryBuilder;

class TelephoneCallGatewayFactoryBuilder extends GatewayFactoryBuilder
{
    /** @var FormFactoryInterface */
    private $formFactory;
    
    /** @var RequestStack */
    private $requestStack;
    
    public function __construct(
        $gatewayFactoryClass,
        FormFactoryInterface $formFactory,
        RequestStack $requestStack
    ) {
        parent::__construct( $gatewayFactoryClass );
        
        $this->formFactory  = $formFactory;
        $this->requestStack = $requestStack;
    }
    
    public function __invoke()
    {
        $factory    = call_user_func_array( [$this, 'build'], func_get_args() );
        $factory->setDependencies( $this->formFactory, $this->requestStack );
        
        return $factory;
    }
}