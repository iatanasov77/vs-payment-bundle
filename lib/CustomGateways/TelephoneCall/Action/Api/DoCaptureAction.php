<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Exception\LogicException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Api;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\DoCapture;

class DoCaptureAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use ApiAwareTrait;
    use GatewayAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritdoc}
     */
    public function execute( $request )
    {
        /** @var $request DoCapture */
        RequestNotSupportedException::assertSupports( $this, $request );

        $model = ArrayObject::ensureArrayObject( $request->getModel() );
        if ( false == $model['auth'] ) {
            throw new LogicException(
                'The auth must be set by DoLogin request but it was not executed or failed. Review payment details model for more information'
            );
        }
        
        $model->replace(
            $this->api->doTelephoneCallPayment( $model )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof DoCapture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
