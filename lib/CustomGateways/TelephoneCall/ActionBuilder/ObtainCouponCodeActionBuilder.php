<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder;

use Payum\Core\Bridge\Spl\ArrayObject;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\ObtainCouponCodeAction;

class ObtainCouponCodeActionBuilder
{
    /** @var FormFactoryInterface */
    private $formFactory;
    
    /** @var RequestStack */
    private $requestStack;
    
    /**
     * @param FormFactoryInterface $formFactory
     * @param RequestStack $requestStack
     */
    public function __construct( FormFactoryInterface $formFactory, RequestStack $requestStack )
    {
        $this->formFactory  = $formFactory;
        $this->requestStack = $requestStack;
    }
    
    /**
     * @param ArrayObject $config
     *
     * @return ObtainCreditCardAction
     */
    public function build( ArrayObject $config )
    {
        $action = new ObtainCouponCodeAction( $this->formFactory, $config['payum.template.obtain_coupon_code'] );
        $action->setRequestStack( $this->requestStack );
        
        return $action;
    }
    
    public function __invoke()
    {
        return call_user_func_array( [$this, 'build'], func_get_args() );
    }
}