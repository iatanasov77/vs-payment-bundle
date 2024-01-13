<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Request\GetStatusInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallResponse;

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

        if ( $model[TelephoneCallResponse::FIELD_STATUS] === TelephoneCallResponse::STATUS_ERROR ) {
            $request->markFailed();
            
            return;
        }
        
        if ( false == $model[TelephoneCallResponse::FIELD_STATUS] && false == $model[TelephoneCallResponse::FIELD_COUPON] ) {
            $request->markNew();
            
            return;
        }
        
        if ( false == $model[TelephoneCallResponse::FIELD_STATUS] && $model[TelephoneCallResponse::FIELD_COUPON] ) {
            $request->markPending();
            
            return;
        }
        
        if ( $model[TelephoneCallResponse::FIELD_STATUS] === TelephoneCallResponse::STATUS_OK && $model[TelephoneCallResponse::FIELD_COUPON] ) {
            $request->markCaptured();
            
            return;
        }
        
        if ( $model[TelephoneCallResponse::FIELD_STATUS] === TelephoneCallResponse::STATUS_OK ) {
            $request->markAuthorized();
            
            return;
        }
        if ( $model[TelephoneCallResponse::FIELD_AUTH]['response'][TelephoneCallResponse::FIELD_STATUS] === TelephoneCallResponse::STATUS_OK ) {
            $request->markAuthorized();
            
            return;
        }
        
        $request->markUnknown();
    }

    /**
     * {@inheritDoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
