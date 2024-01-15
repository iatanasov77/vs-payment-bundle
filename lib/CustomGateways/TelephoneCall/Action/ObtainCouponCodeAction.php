<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Symfony\Reply\HttpResponse;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\RenderTemplate;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\ObtainCouponCode;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder\CouponCodeForm;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder\CouponCodeInterface;

class ObtainCouponCodeAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
    
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    
    /**
     * @var Request
     */
    protected $httpRequest;
    
    /**
     * @var RequestStack
     */
    protected $httpRequestStack;
    
    /**
     * @var array
     */
    protected $coreGatewayConfig;
    
    /**
     * @var string
     */
    protected $templateName;
    
    /**
     * @param FormFactoryInterface $formFactory
     * @param string               $templateName
     */
    public function __construct( FormFactoryInterface $formFactory, $templateName = null )
    {
        $this->formFactory  = $formFactory;
        $this->templateName = $templateName;
    }
    
    /**
     * @param Request $request
     * @deprecated
     */
    public function setRequest( Request $request = null )
    {
        $this->httpRequest = $request;
    }
    
    /**
     * @param RequestStack|null $requestStack
     */
    public function setRequestStack( RequestStack $requestStack = null )
    {
        $this->httpRequestStack = $requestStack;
    }
    
    public function setCoreGatewayConfig( array $config = [] )
    {
        if ( ! $this->templateName ) {
            $this->templateName = $config['payum.template.obtain_coupon_code'];
        }
    }
    
    /**
     * {@inheritDoc}
     *
     * @param ObtainCouponCode $request
     */
    public function execute( $request )
    {
        RequestNotSupportedException::assertSupports( $this, $request );
        
        $httpRequest = null;
        if ( $this->httpRequest instanceof Request ) {
            $httpRequest = $this->httpRequest;
        } elseif ( $this->httpRequestStack instanceof RequestStack ) {
            
            # BC Layer for Symfony 4 (Simplify after support for Symfony < 5 is dropped)
            if ( method_exists( $this->httpRequestStack, 'getMainRequest' ) ) {
                $httpRequest = $this->httpRequestStack->getMainRequest();
            } else {
                $httpRequest = $this->httpRequestStack->getMasterRequest();
            }
        }
        
        if ( false == $httpRequest ) {
            throw new LogicException( 'The action can be run only when http request is set.' );
        }
        
        $form = $this->createCouponCodeForm();
        
        $form->handleRequest( $httpRequest );
        if ( $form->isSubmitted() ) {
            /** @var CouponCodeInterface $couponCode */
            $couponCode = $form->getData();
            
            if ( $form->isValid() ) {
                $request->set( $couponCode );
                
                return;
            }
        }
        
        $renderTemplate = new RenderTemplate( $this->templateName, [
            'model'         => $request->getModel(),
            'firstModel'    => $request->getFirstModel(),
            'form'          => $form->createView(),
            'actionUrl'     => $request->getToken() ? $request->getToken()->getTargetUrl() : null,
        ]);
        $this->gateway->execute( $renderTemplate );
        
        throw new HttpResponse( new Response( $renderTemplate->getResult(), 200, [
            'Cache-Control' => 'no-store, no-cache, max-age=0, post-check=0, pre-check=0',
            'X-Status-Code' => 200,
            'Pragma'        => 'no-cache',
        ]));
    }
    
    /**
     * {@inheritDoc}
     */
    public function supports( $request )
    {
        return $request instanceof ObtainCouponCode;
    }
    
    /**
     * @return FormInterface
     */
    protected function createCouponCodeForm()
    {
        return $this->formFactory->create( CouponCodeForm::class );
    }
}