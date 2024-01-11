<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\ObtainToken;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\DoCapture;

class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Capture $request
     */
    public function execute( $request )
    {
        RequestNotSupportedException::assertSupports( $this, $request );

        $model = ArrayObject::ensureArrayObject( $request->getModel() );

        if ( $model['status'] ) {
            return;
        }
        
        if ( false == $model['auth'] ) {
            $this->gateway->execute( new Authorize( $model ) );
        }
        
        if ( false == $model['card'] ) {
            $obtainToken    = new ObtainToken( $request->getToken() );
            $obtainToken->setModel( $model );
            
            $this->gateway->execute( $obtainToken );
        }
        
        $this->gateway->execute( new DoCapture( $model ) );
    }

    /**
     * {@inheritDoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
