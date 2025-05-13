<?php namespace Vankosoft\PaymentBundle\Component\Payum\Stripe\Action\Api;

use Composer\InstalledVersions;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\ApiAwareTrait;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Stripe\Constants;
use Payum\Stripe\Keys;
use Stripe\Stripe;
use Stripe\Exception;
use Stripe\Coupon;

use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\DeleteCoupon;

class DeleteCouponAction implements ActionInterface, GatewayAwareInterface, ApiAwareInterface
{
    use ApiAwareTrait {
        setApi as _setApi;
    }
    use GatewayAwareTrait;
    
    /**
     * @deprecated BC will be removed in 2.x. Use $this->api
     *
     * @var Keys
     */
    protected $keys;
    
    public function __construct()
    {
        $this->apiClass = Keys::class;
    }
    
    /**
     * {@inheritDoc}
     */
    public function setApi( $api )
    {
        $this->_setApi( $api );
        
        // BC. will be removed in 2.x
        $this->keys = $this->api;
    }
    
    /**
     * {@inheritDoc}
     */
    public function execute( $request )
    {
        /** @var $request DeleteCoupon */
        RequestNotSupportedException::assertSupports( $this, $request );
        
        $model = ArrayObject::ensureArrayObject( $request->getModel() );
        
        try {
            Stripe::setApiKey( $this->keys->getSecretKey() );
            
            if ( class_exists( InstalledVersions::class ) ) {
                Stripe::setAppInfo(
                    Constants::PAYUM_STRIPE_APP_NAME,
                    InstalledVersions::getVersion( 'stripe/stripe-php' ),
                    Constants::PAYUM_URL
                );
            }
            
            $coupon = Coupon::retrieve( $model['id'] );
            if ( $coupon ) {
                $deletedCoupon  = $coupon->delete();
            }
            
            $model->replace( $deletedCoupon->toArray( true ) );
        } catch ( Exception\ApiErrorException $e ) {
            $model->replace( $e->getJsonBody() );
        }
    }
    
    /**
     * {@inheritDoc}
     */
    public function supports( $request ): bool
    {
        return
            $request instanceof DeleteCoupon &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}