<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Request\Sync;
use Payum\Core\Exception\RequestNotSupportedException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\DoLogin;

class AuthorizeAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritDoc}
     *
     * @param Authorize $request
     */
    public function execute( $request )
    {
        RequestNotSupportedException::assertSupports( $this, $request );

        $details    = ArrayObject::ensureArrayObject( $request->getModel() );

        if ( false == isset( $details['auth'] ) ) {
            $this->gateway->execute( new DoLogin( $details ) );
            $this->gateway->execute( new Sync( $details ) );
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof Authorize &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
