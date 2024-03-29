<?php namespace Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Authorize;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\LogicException;

use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\TelephoneCallResponse;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\ObtainCouponCode;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\Request\Api\DoCapture;
use Vankosoft\PaymentBundle\CustomGateways\TelephoneCall\ActionBuilder\CouponCodeInterface;

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
        
        if ( false == $model[TelephoneCallResponse::FIELD_AUTH] ) {
            $this->gateway->execute( new Authorize( $model ) );
        }
        
        if ( false == $model['coupon'] ) {
            try {
                $obtainCouponCode = new ObtainCouponCode( $request->getToken() );
                $obtainCouponCode->setModel( $request->getFirstModel() );
                $obtainCouponCode->setModel( $request->getModel() );
                $this->gateway->execute( $obtainCouponCode );
                
                /** @var CouponCodeInterface */
                $coupon = $obtainCouponCode->obtain();
                
                $model['coupon_code'] = $coupon->getCouponCode();
            } catch ( RequestNotSupportedException $e ) {
                throw new LogicException( 'Coupon code details has to be set explicitly or there has to be an action that supports ObtainCouponCode request.' );
            }
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
