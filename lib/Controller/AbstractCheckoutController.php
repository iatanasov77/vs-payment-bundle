<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Payum\Core\Payum;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\CreditCard;

use Vankosoft\PaymentBundle\Exception\ShoppingCardException;

abstract class AbstractCheckoutController extends AbstractController
{
    /** @var EntityRepository */
    protected $ordersRepository;
    
    /** @var Payum */
    protected $payum;
    
    /** @var string */
    protected $paymentClass;
    
    /** @var \Payum\Core\Gateway  */
    protected $gateway;
    
    /** @var string */
    protected $gatewayName;
    
    public function __construct(
        EntityRepository $ordersRepository,
        Payum $payum,
        string $paymentClass
    ) {
        $this->ordersRepository         = $ordersRepository;
        $this->payum                    = $payum;
        $this->paymentClass             = $paymentClass;
        
        /**
         * NOTE: $this->gatewayName shold be initialized in Child Classes
         */
        if ( $this->gatewayName ) {
            $this->gateway  = $this->payum->getGateway( $this->gatewayName );
        }
    }
    
    abstract public function prepareAction( Request $request ): Response;
    
    public function doneAction( Request $request ): Response
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $this->payum->getHttpRequestVerifier()->invalidate( $token );  // you can invalidate the token. The url could not be requested any more.
        
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        $gateway->execute( $status = new GetHumanStatus( $token ) );
        
        $payment    = $status->getFirstModel();
        // using shortcut
        if ( $status->isCaptured() || $status->isAuthorized() ) {
            // success
            return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
                'paymentStatus' => $status,
            ]);
        }
        
        // using shortcut
        if ( $status->isPending() ) {
            // most likely success, but you have to wait for a push notification.
            return $this->render( '@VSPayment/Pages/Checkout/done.html.twig', [
                'paymentStatus' => $status,
            ]);
        }
        
        // using shortcut
        if ( $status->isFailed() || $status->isCanceled() ) {
            throw new HttpException( 400, $this->getErrorMessage( $status->getModel() ) );
        }
    }
/*
    public function doneAction( Request $request )
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        
        $gateway->execute( $status = new GetHumanStatus( $token ) );
        
        echo '<pre>'; var_dump( $status ); die;
    }
*/
    protected function getShoppingCard()
    {
        $cardId = $this->get('session')->get( 'vs_payment_basket_id' );
        if ( ! $cardId ) {
            throw new ShoppingCardException( 'Card not exist in session !!!' );
        }
        $card   = $this->ordersRepository->find( $cardId );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card not exist in repository !!!' );
        }
        
        return $card;
    }
    
    protected function createCreditCard( $details )
    {
        $card = new CreditCard();
        
        $card->setNumber( $details['number'] );
        $card->setExpireAt( new \DateTime('2018-10-10') );
        $card->setSecurityCode( $details['cvv'] );
        $card->setHolder( $details['holdere'] );
        
        return $card;
    }
    
    protected function getErrorMessage( $details )
    {
        return 'STRIPE ERROR: ' . $details['error']['message'];
    }
}
