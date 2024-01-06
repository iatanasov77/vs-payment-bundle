<?php namespace Vankosoft\PaymentBundle\Component\Payment;

use Symfony\Component\Routing\RouterInterface;
use Doctrine\Persistence\ManagerRegistry;
use Payum\Offline\Constants as PayumOfflineConstants;
use Payum\Paypal\ExpressCheckout\Nvp\Api as PaypalApi;
use Payum\Paypal\ProCheckout\Nvp\Api as PaypalProApi;
use Vankosoft\UsersSubscriptionsBundle\Component\PayedService\SubscriptionPeriod;
use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Model\Interfaces\GatewayConfigInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Exception\GatewayException;
use Vankosoft\PaymentBundle\Component\Exception\PricingPlanException;

final class Payment
{
    const TOKEN_STORAGE_FILESYSTEM      = 'filesystem';
    const TOKEN_STORAGE_DOCTRINE_ORM    = 'doctrine_orm';
    
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RouterInterface */
    private $router;
    
    /** @var OrderFactory */
    private $orderFactory;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RouterInterface $router,
        OrderFactory $orderFactory
    ) {
        $this->doctrine     = $doctrine;
        $this->router       = $router;
        $this->orderFactory = $orderFactory;
    }
    
    public function getPaymentPrepareRoute( GatewayConfigInterface $gatewayConfig, $isRecurring = false )
    {
        switch( $gatewayConfig->getFactoryName() ) {
            case 'offline':
                $route  = 'vs_payment_offline_prepare';
                break;
            case 'offline_bank_transfer':
                $route  = 'vs_payment_offline_bank_transfer_prepare';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = 'vs_payment_stripe_checkout_prepare';
//                 $route  = $isRecurring ?
//                             'vs_payment_stripe_checkout_recurring_prepare':
//                             'vs_payment_stripe_checkout_prepare';
                break;
            case 'stripe_coupon':
                $route  = 'vs_payment_stripe_checkout_coupon_prepare';
                break;
            case 'paypal_express_checkout':
                $route  = 'vs_payment_paypal_express_checkout_prepare';
                break;
            case 'paypal_pro_checkout':
                $route  = 'vs_payment_paypal_pro_checkout_prepare';
                break;
            case 'paysera':
                $route  = 'vs_payment_paysera_prepare';
                break;
            case 'borica':
                $route  = 'vs_payment_borica_prepare';
                break;
            case 'authorize_net_aim':
                $route  = 'vs_payment_authorize_net_prepare';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
    
    public function getPaymentCreateRecurringUrl( PricingPlanSubscriptionInterface $subscription )
    {
        switch( $subscription->getGateway()->getFactoryName() ) {
            case 'offline':
                $route  = '';
                break;
            case 'offline_bank_transfer':
                $route  = '';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = $this->router->generate( 'vs_payment_stripe_checkout_create_recurring_payment', [
                    'subscriptionId' => $subscription->getId()
                ]);
                break;
            case 'paypal_express_checkout':
                $route  = $this->router->generate( 'vs_payment_paypal_express_checkout_create_recurring_agreement', [
                    'subscriptionId' => $subscription->getId()
                ]);
                break;
            case 'paypal_pro_checkout':
                $route  = '';
                break;
            case 'paysera':
                $route  = '';
                break;
            case 'borica':
                $route  = '';
                break;
            case 'authorize_net_aim':
                $route  = '';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
    
    public function getPaymentCancelRecurringUrl( PricingPlanSubscriptionInterface $subscription )
    {
        switch( $subscription->getGateway()->getFactoryName() ) {
            case 'offline':
                $route  = '';
                break;
            case 'offline_bank_transfer':
                $route  = '';
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                $route  = $this->router->generate( 'vs_payment_stripe_checkout_cancel_recurring_payment', [
                    'subscriptionId' => $subscription->getId()
                ]);
                break;
            case 'paypal_express_checkout':
                $route  = $this->router->generate( 'vs_payment_paypal_express_checkout_cancel_recurring_payment', [
                    'subscriptionId' => $subscription->getId()
                ]);
                break;
            case 'paypal_pro_checkout':
                $route  = '';
                break;
            case 'paysera':
                $route  = '';
                break;
            case 'borica':
                $route  = '';
                break;
            case 'authorize_net_aim':
                $route  = '';
                break;
            default:
                $route  = 'not_configured';
        }
        
        return $route;
    }
    
    public function isGatewaySupportRecurring( GatewayConfigInterface $gatewayConfig ): bool
    {
        switch( $gatewayConfig->getFactoryName() ) {
            case 'offline':
                return false;
                break;
            case 'offline_bank_transfer':
                return false;
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                return true;
                break;
            case 'paypal_express_checkout':
                return true;
                break;
            case 'paypal_pro_checkout':
                return false;
                break;
            case 'paysera':
                return false;
                break;
            case 'borica':
                return false;
                break;
            case 'authorize_net_aim':
                return false;
                break;
            default:
                throw new GatewayException( 'Unknown Gateawy Factory !!!' );
        }
    }
    
    public function isPaymentPaid( PaymentInterface $payment ): bool
    {
        if ( ! $payment->getOrder() ) {
            return false;
        }
        
        $paymentDetails = $payment->getDetails();
        $paymentFactory = $payment->getOrder()->getPaymentMethod()->getGateway()->getFactoryName();
        
        switch( $paymentFactory ) {
            case 'offline':
                return false;
                break;
            case 'offline_bank_transfer':
                return isset( $paymentDetails['paid'] ) && \boolval( $paymentDetails['paid'] );
                break;
            case 'stripe_checkout':
            case 'stripe_js':
                return isset( $paymentDetails['paid'] ) && \boolval( $paymentDetails['paid'] );
                break;
            case 'paypal_express_checkout':
                return isset( $paymentDetails['ACK'] ) && ( $paymentDetails['ACK'] == PaypalApi::ACK_SUCCESS );
                break;
            case 'paypal_pro_checkout':
                return isset( $paymentDetails['RESULT'] ) && ( \intval( $paymentDetails['RESULT'] ) == PaypalProApi::RESULT_SUCCESS );
                break;
            case 'paysera':
                return false;
                break;
            case 'borica':
                return false;
                break;
            case 'authorize_net_aim':
                return isset( $paymentDetails['approved'] ) && \boolval( $paymentDetails['approved'] );
                break;
            default:
                throw new GatewayException( 'Unknown Gateawy Factory !!!' );
        }
    }
    
    public function getPaypalNvpBillingCycle( string $period ): array
    {
        $billingCycle = null;
        
        switch( $period ) {
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_UNLIMITED:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_YEAR,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_YEAR:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_YEAR,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_HALFYEAR:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_MONTH,
                    'frequency' => 6,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_QUARTERYEAR:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_DAY,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_MONTH:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_MONTH,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_SEMIMONTH:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_SEMIMONTH,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_WEEK:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_WEEK,
                    'frequency' => 1,
                ];
                break;
            case SubscriptionPeriod::SUBSCRIPTION_PERIOD_DAY:
                $billingCycle   = [
                    'period'    => PaypalApi::BILLINGPERIOD_DAY,
                    'frequency' => 1,
                ];
                break;
            default:
                throw new PricingPlanException( 'Unknown Pricing Plan Subscription Period' );
        }
        
        return $billingCycle;
    }
    
    public function setBankTransferPaymentPaid( PaymentInterface $payment )
    {
        $em     = $this->doctrine->getManager();
        $order  = $payment->getOrder();
        if ( $order ) {
            $order->setStatus( Order::STATUS_PENDING_ORDER );
            $subscriptions  = $order->getSubscriptions();
            if ( ! empty( $subscriptions ) ) {
                foreach ( $subscriptions as $subscription ) {
                    $previousSubscription   = $order->getUser()->getActivePricingPlanSubscriptionByService(
                        $subscription->getPricingPlan()->getPaidService()->getPayedService()
                        );
                    if ( $previousSubscription ) {
                        $previousSubscription->setActive( false );
                        $em->persist( $previousSubscription );
                    }
                    
                    $subscription->setActive( true );
                    $em->persist( $subscription );
                }
            }
            
            $em->persist( $order );
        }
        
        $paymentDetails = $payment->getDetails();
        $paymentDetails[PayumOfflineConstants::FIELD_PAID]  = true;
        
        $payment->setDetails( $paymentDetails );
        $em->persist( $payment );
        $em->flush();
    }
}
