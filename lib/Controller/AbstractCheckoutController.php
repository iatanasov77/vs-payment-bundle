<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Payum\Bundle\PayumBundle\Controller\PayumController;
use Payum\Core\Request\GetHumanStatus;

abstract class AbstractCheckoutController extends PayumController
{
    
    abstract public function prepareAction( Request $request );
    
    public function doneAction( Request $request )
    {
        $token      = $this->getPayum()->getHttpRequestVerifier()->verify( $request );
        
        $this->getPayum()->getHttpRequestVerifier()->invalidate( $token );  // you can invalidate the token. The url could not be requested any more.
        
        $gateway    = $this->getPayum()->getGateway( $token->getGatewayName() );
        $status     = new GetHumanStatus( $token );
        $gateway->execute( $status );
        
        if ( ! $status->isCaptured() ) {
            if ( $status->isFailed() ) {
                throw new HttpException( 400, $this->getErrorMessage( $status->getModel() ) );
            } else {
                throw new HttpException( 400, 'The payum gateway status is: ' . $status->getValue() );
            }
        }
        
        return $this->redirect( $this->generateUrl( 'ia_paid_membership_subscription_create', ['paymentId' => $status->getFirstModel()->getId()] ) );
    }
    
    abstract protected function gatewayName();
    
    abstract protected function getErrorMessage( $details );
    
    protected function createCreditCard( $details )
    {
        $card = new \Payum\Core\Model\CreditCard();
        
        $card->setNumber( $details['number'] );
        $card->setExpireAt(new \DateTime('2018-10-10'));
        $card->setSecurityCode( $details['cvv'] );
        $card->setHolder( $details['holdere'] );
        
        return $card;
    }
}
