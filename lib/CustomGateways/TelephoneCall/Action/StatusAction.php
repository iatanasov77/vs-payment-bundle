<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Constants;

class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     *
     * @param GetStatusInterface $request
     */
    public function execute( $request )
    {
        RequestNotSupportedException::assertSupports( $this, $request );

        $model = ArrayObject::ensureArrayObject( $request->getModel() );

        if ( $model['error'] ) {
            $request->markFailed();
            
            return;
        }
        
        if ( false == $model['status'] && false == $model['card'] ) {
            $request->markNew();
            
            return;
        }
        
        if ( false == $model['status'] && $model['card'] ) {
            $request->markPending();
            
            return;
        }
        
        if ( Constants::STATUS_FAILED == $model['status'] ) {
            $request->markFailed();
            
            return;
        }
        
        if ( Constants::STATUS_SUCCEEDED == $model['status'] && $model['captured'] && $model['paid'] ) {
            $request->markCaptured();
            
            return;
        }
        
        if ( Constants::STATUS_PAID == $model['status'] && $model['captured'] && $model['paid'] ) {
            $request->markCaptured();
            
            return;
        }
        
        
        if ( Constants::STATUS_SUCCEEDED == $model['status'] && false == $model['captured'] ) {
            $request->markAuthorized();
            
            return;
        }
        if ( Constants::STATUS_PAID == $model['status'] && false == $model['captured'] ) {
            $request->markAuthorized();
            
            return;
        }
        
        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports( $request )
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
