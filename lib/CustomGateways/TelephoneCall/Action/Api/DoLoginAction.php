<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action\Api;

use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Api;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\DoLogin;

class DoLoginAction implements ActionInterface, ApiAwareInterface
{
    use ApiAwareTrait;

    public function __construct()
    {
        $this->apiClass = Api::class;
    }

    /**
     * {@inheritdoc}
     */
    public function execute( $request )
    {
        /** @var $request DoLogin */
        RequestNotSupportedException::assertSupports( $this, $request );

        $model = ArrayObject::ensureArrayObject( $request->getModel() );
        if ( isset( $model['auth'] ) ) {
            return;
        }
        
        $model->replace( $this->api->doLogin() );
    }

    /**
     * {@inheritdoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof DoLogin &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
