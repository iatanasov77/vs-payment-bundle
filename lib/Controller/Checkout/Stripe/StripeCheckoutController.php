<?php namespace Vankosoft\PaymentBundle\Controller\Checkout\Stripe;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\ManagerRegistry;

use Payum\Core\Payum;
use Payum\Stripe\Request\Api\CreateSubscription;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Component\Catalog\CatalogBridgeInterface;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;
use Vankosoft\CatalogBundle\Model\Interfaces\PricingPlanSubscriptionInterface;

/**
 * USED MANUALS:
 * =============
 * https://stackoverflow.com/questions/34908805/create-a-recurring-or-subscription-payment-using-payum-stripe-on-symfony-2
 * https://github.com/Payum/Payum/blob/master/docs/stripe/subscription-billing.md
 * https://github.com/Payum/PayumBundle
 *
 * MANUALS for Overriding Payum Stripe Bundle templates
 * =====================================================
 * https://github.com/Payum/PayumBundle/issues/326
 * https://stackoverflow.com/questions/28452317/stripe-checkout-with-custom-form-symfony
 * https://github.com/makasim/PayumBundleSandbox/blob/ffea27445d6774dfdc8e646b914e9b58cbfa9765/src/Acme/PaymentBundle/Controller/SimplePurchaseStripeViaOmnipayController.php#L36
 *
 * https://github.com/Payum/Payum/blob/master/docs/stripe/store-card-and-use-later.md
 * 
 * Create Stripe Recurring Payments
 * =================================
 * https://github.com/Payum/Payum/blob/master/docs/stripe/subscription-billing.md
 * 
 * 
 * OmnipayBridge is Very Old
 * ==========================
 * https://github.com/Payum/OmnipayBridge/blob/master/composer.json
 */
class StripeCheckoutController extends AbstractCheckoutRecurringController
{
    /** @var StripeApi */
    private $stripeApi;
    
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        Payum $payum,
        SecurityBridge $securityBridge,
        Payment $vsPayment,
        OrderFactory $orderFactory,
        CatalogBridgeInterface $subscriptionsBridge,
        string $paymentClass,
        bool $throwExceptionOnPaymentDone,
        ?string $routeRedirectOnShoppingCartDone,
        ?string $routeRedirectOnPricingPlanDone,
        ?string $routeRedirectOnSubscriptionActionDone,
        StripeApi $stripeApi
    ) {
        parent::__construct(
            $eventDispatcher,
            $translator,
            $doctrine,
            $payum,
            $securityBridge,
            $vsPayment,
            $orderFactory,
            $subscriptionsBridge,
            $paymentClass,
            $throwExceptionOnPaymentDone,
            $routeRedirectOnShoppingCartDone,
            $routeRedirectOnPricingPlanDone,
            $routeRedirectOnSubscriptionActionDone
        );
        
        $this->stripeApi    = $stripeApi;
    }
    
    public function prepareAction( Request $request ): Response
    {
        $cart           = $this->orderFactory->getShoppingCart();
        $payment        = $this->preparePayment( $cart );
        
        $captureToken   = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_stripe_checkout_done' // the route to redirect after capture
        );
        
        if ( $cart->getPaymentMethod()->getGateway()->getFactoryName() == 'stripe_js' ) {
            $captureUrl = base64_encode( $captureToken->getTargetUrl() );
            return $this->redirect( $this->generateUrl( 'vs_payment_show_credit_card_form', ['formAction' => $captureUrl] ) );
        } else {
            return $this->redirect( $captureToken->getTargetUrl() );
        }
    }
    
    public function createRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        $subscriptionsRepository    = $this->subscriptionsBridge->getRepository();
        
        $subscription   = $subscriptionsRepository->find( $subscriptionId );
        $gtAttributes   = $this->checkSubscriptionAttributes( $request, $subscription );
        $redirectRoute  = null;
        
        if ( ! \is_array( $gtAttributes ) ) {
            $redirectRoute  = 'vs_payment_pricing_plans';
        }
        
        if ( $this->checkRecurringPaymentCreated( $request, $gtAttributes, false ) ) {
            $redirectRoute  = 'vs_payment_pricing_plans';
        }
        
        if ( $redirectRoute && $request->isXmlHttpRequest() ) {
            return $this->jsonResponse( Status::STATUS_ERROR, $redirectRoute );
        } else {
            return $this->redirectToRoute( $redirectRoute );
        }
        
        $this->_createRecurringPayment( $subscription, $gtAttributes );
        
        $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_create_subscription_recurring_success', [], 'VSPaymentBundle' );
        $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        
        if ( $this->routeRedirectOnPricingPlanDone ) {
            $redirectRoute  = $this->routeRedirectOnPricingPlanDone;
        } else {
            $redirectRoute  = 'vs_payment_pricing_plans';
        }
        
        if ( $redirectRoute && $request->isXmlHttpRequest() ) {
            return $this->jsonResponse( Status::STATUS_ERROR, $redirectRoute );
        } else {
            return $this->redirectToRoute( $redirectRoute );
        }
    }
    
    public function cancelRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        $subscriptionsRepository    = $this->subscriptionsBridge->getRepository();
        
        $subscription   = $subscriptionsRepository->find( $subscriptionId );
        $gtAttributes   = $this->checkSubscriptionAttributes( $request, $subscription );
        
        if ( ! \is_array( $gtAttributes ) ) {
            $redirectRoute  = 'vs_payment_pricing_plans';
        }
        
        if ( ! $this->checkRecurringPaymentCreated( $request, $gtAttributes, true ) ) {
            $redirectRoute  = 'vs_payment_pricing_plans';
        }
        
        $this->_cancelRecurringPayment( $subscription, $gtAttributes );
        
        $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_cancel_subscription_recurring_success', [], 'VSPaymentBundle' );
        $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        
        $redirectRoute  = $this->routeRedirectOnSubscriptionActionDone ?: 'vs_payment_pricing_plans';
        if ( $redirectRoute && $request->isXmlHttpRequest() ) {
            return $this->jsonResponse( Status::STATUS_OK, $redirectRoute );
        } else {
            return $this->redirectToRoute( $redirectRoute );
        }
    }
    
    protected function preparePayment( OrderInterface $cart )
    {
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->securityBridge->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        // Payment Details
        $paymentDetails   = $this->preparePaymentDetails( $cart );
        $payment->setDetails( $paymentDetails );
        
        $this->doctrine->getManager()->persist( $cart );
        $this->doctrine->getManager()->flush();
        $storage->update( $payment );
        
        return $payment;
    }
    
    protected function preparePaymentDetails( OrderInterface $cart ): array
    {
        $paymentDetails   = [
            'local' => [
                'save_card' => true,
            ]
        ];
        
        $subscriptions  = $cart->getSubscriptions();
        $hasPricingPlan = ! empty( $subscriptions );
        
        if ( $hasPricingPlan ) {
            $gateway        = $cart->getPaymentMethod()->getGateway();
            $pricingPlan    = $subscriptions[0]->getPricingPlan();
            $gtAttributes   = $pricingPlan->getGatewayAttributes();
            
            if (
                $this->vsPayment->isGatewaySupportRecurring( $gateway ) &&
                $cart->hasRecurringPayment() &&
                \array_key_exists( StripeApi::PRICING_PLAN_ATTRIBUTE_KEY, $gtAttributes )
            ) {
                $user               = $this->securityBridge->getUser();
                $userPaymentDetails = $user ? $user->getPaymentDetails() : [];
                
                // Subscribing a customer to a plan
                if ( isset( $userPaymentDetails[StripeApi::CUSTOMER_ATTRIBUTE_KEY] ) ) {
                    $paymentDetails['local']['customer']['id'] = $userPaymentDetails[StripeApi::CUSTOMER_ATTRIBUTE_KEY];
                }
                $paymentDetails['local']['customer']['plan'] = $gtAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY];
            }
        }
        
        return $paymentDetails;
    }
    
    /**
     * 
     * @param Request $request
     * @param array $gtAttributes
     * 
     * @return array|null
     */
    private function checkSubscriptionAttributes( Request $request, PricingPlanSubscriptionInterface $subscription ): ?array
    {
        $gtAttributes   = $subscription->getGatewayAttributes();
        $gtAttributes   = $gtAttributes ?: [];
        
        if ( ! isset( $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY] ) ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_subscription_missing_attributes', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'error', $flashMessage );
            
            return null;
        }
        
        $ppAttributes   = $subscription->getPricingPlan()->getGatewayAttributes();
        if ( ! isset( $ppAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY] ) ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_subscription_missing_attributes', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'error', $flashMessage );
            
            return null;
        }
        $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]   = $ppAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY];
        
        return $gtAttributes;
    }
    
    /**
     * 
     * @param Request $request
     * @param array $gtAttributes
     * @param bool $needCreated
     * 
     * @return bool
     */
    private function checkRecurringPaymentCreated( Request $request, array $gtAttributes, bool $needCreated ): bool
    {
        if ( $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY] && isset( $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY] ) ) {
            if ( ! $needCreated ) {
                $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_subscription_already_created', [], 'VSPaymentBundle' );
                $request->getSession()->getFlashBag()->add( 'error', $flashMessage );
            }
            
            return true;
        }
        
        if ( $needCreated ) {
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_subscription_not_created_yet', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'error', $flashMessage );
        }
        
        return false;
    }
    
    /**
     * 
     * @param PricingPlanSubscriptionInterface $subscription
     * @param array $gtAttributes
     */
    private function _createRecurringPayment( PricingPlanSubscriptionInterface $subscription, array $gtAttributes ): void
    {
        $cart           = $this->orderFactory->getShoppingCart();
        //$payment        = $this->preparePayment( $cart );
        
        //$gateway        = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getFactoryName() );
        $gateway        = $this->payum->getGateway( $subscription->getGatewayFactory() );
        
        $stripeRequest  = new \ArrayObject([
            'customer'  => $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY],
            'items'     => [
                ['price' => $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]]
            ],
        ]);
        $gateway->execute( $subscriptionData = new CreateSubscription( $stripeRequest ) );
        $subscriptionModel                                      = $subscriptionData->getModel();
        $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY]    = $subscriptionModel['id'];
        
        $subscription->setGatewayAttributes( $gtAttributes );
        $subscription->setRecurringPayment( true );
        
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
    }
    
    /**
     * 
     * @param PricingPlanSubscriptionInterface $subscription
     * @param array $gtAttributes
     */
    private function _cancelRecurringPayment( PricingPlanSubscriptionInterface $subscription, array $gtAttributes ): void
    {
        $this->stripeApi->cancelSubscription( $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY] );
        
        $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]   = null;
        unset( $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY] );
        
        $subscription->setGatewayAttributes( $gtAttributes );
        $subscription->setRecurringPayment( false );
        
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
    }
}
