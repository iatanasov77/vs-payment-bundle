<?php namespace Vankosoft\PaymentBundle\Controller\CheckoutTest;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;


use Payum\Core\Payum;
use Payum\Stripe\Request\Api\CreatePlan;
use Payum\Core\Model\Payment;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;


use Payum\Core\Model\CreditCard;
use Payum\Core\Request\GetHttpRequest;
use Payum\Stripe\Action\Api\ObtainTokenAction;
use Payum\Stripe\Request\Api\CreateCustomer;
use Payum\Stripe\StripeJsGatewayFactory;
use Payum\Stripe\StripeCheckoutGatewayFactory;




/**
 * USED MANUALS:
 * =============
 * https://stackoverflow.com/questions/34908805/create-a-recurring-or-subscription-payment-using-payum-stripe-on-symfony-2
 * https://github.com/Payum/Payum/blob/master/docs/stripe/subscription-billing.md
 * https://github.com/Payum/PayumBundle
 * 
 * MANUALS for Overriding Payum Stripe Bundle templates
 * =====================================================
 * https://github.com/Payum/PayumBundle/issues/326
 * https://stackoverflow.com/questions/28452317/stripe-checkout-with-custom-form-symfony
 * https://github.com/makasim/PayumBundleSandbox/blob/ffea27445d6774dfdc8e646b914e9b58cbfa9765/src/Acme/PaymentBundle/Controller/SimplePurchaseStripeViaOmnipayController.php#L36
 * 
 * OmnipayBridge is Very Old
 * ==========================
 * https://github.com/Payum/OmnipayBridge/blob/master/composer.json
 */
class StripeCheckoutController extends AbstractController
{
    /** @var string */
    protected $paymentClass;
    
    /** @var EntityRepository */
    protected $paidServicesRepository;
    
    /** @var Payum */
    protected $payum;
    
    protected $gateway;
    
    public function __construct(
        string $paymentClass,
        EntityRepository $paidServicesRepository,
        Payum $payum
    ) {
        $this->paymentClass             = $paymentClass;
        $this->paidServicesRepository   = $paidServicesRepository;
        $this->payum                    = $payum;
        
        $this->gateway                  = $this->payum->getGateway( 'stripe_checkout' );
    }
    
    public function prepareAction( Request $request )
    {
        try {
            $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        } catch( \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e ) {
            // DO NOTHING :)
        }
        if ( isset( $token ) ) {
            var_dump( $token ); die;
        }
        
        // 4111 1111 1111 1111
        $card = new CreditCard();
        $card->setNumber( '4111111111111111' );
        $card->setExpireAt( ( new \DateTime() )->add( new \DateInterval( "P1M" ) ) );
        $card->setSecurityCode( '123' );
        $card->setHolder( 'Test' );

        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setPaidService( $this->paidServicesRepository->find( 1 ) );
        
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( 'EUR' );
        $payment->setTotalAmount( 123 ); // 1.23 EUR
        $payment->setDescription( 'A description' );
        
        $payment->setClientId( $this->getUser()->getId() );
        $payment->setClientEmail( $this->getUser()->getEmail() );
        
        $payment->setCreditCard( $card );
        
        $storage->update( $payment );
        
        
        $this->gateway->execute( new Capture( $payment ) );
        
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            'stripe_checkout',
            //'stripe_js',
            $payment,
            'wgp_stripe_done' // the route to redirect after capture
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
        
    }
    
    public function doneAction( Request $request )
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        
        $gateway->execute( $status = new GetHumanStatus( $token ) );
        
        echo '<pre>'; var_dump( $status ); die;
    }
}
