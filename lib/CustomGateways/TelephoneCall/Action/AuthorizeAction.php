<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallResponse;
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

        if ( false == isset( $details[TelephoneCallResponse::FIELD_AUTH] ) ) {
            $this->gateway->execute( new DoLogin( $details ) );
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
