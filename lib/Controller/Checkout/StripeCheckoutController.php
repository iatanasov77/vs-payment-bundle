<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Payum\Stripe\Request\Api\CreateSubscription;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Request\Api\CancelSubscription;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;
use Vankosoft\PaymentBundle\Component\Payum\Stripe\Api as StripeApi;

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
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $gtAttributes   = $this->checkSubscriptionAttributes( $request, $subscription );
        
        if ( ! \is_array( $gtAttributes ) ) {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        if ( $this->checkRecurringPaymentCreated( $request, $gtAttributes, false ) ) {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        $this->_createRecurringPayment( $subscription, $gtAttributes );
        
        $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_create_subscription_recurring_success', [], 'VSPaymentBundle' );
        $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        
        if ( $this->routeRedirectOnPricingPlanDone ) {
            return $this->redirectToRoute( $this->routeRedirectOnPricingPlanDone );
        } else {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
    }
    
    public function cancelRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        // $paymentDetails['local']['customer']['subscriptions']['data'][0]['id']
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $gtAttributes   = $this->checkSubscriptionAttributes( $request, $subscription );
        
        if ( ! \is_array( $gtAttributes ) ) {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        if ( ! $this->checkRecurringPaymentCreated( $request, $gtAttributes, true ) ) {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
        
        $this->_cancelRecurringPayment( $subscription, $gtAttributes );
        
        $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_cancel_subscription_recurring_success', [], 'VSPaymentBundle' );
        $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        
        if ( $this->routeRedirectOnPricingPlanDone ) {
            return $this->redirectToRoute( $this->routeRedirectOnPricingPlanDone );
        } else {
            return $this->redirectToRoute( 'vs_payment_pricing_plans' );
        }
    }
    
    protected function preparePayment( OrderInterface $cart )
    {
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->tokenStorage->getToken()->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        $payment->setOrder( $cart );
        
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
                $gateway->getSupportRecurring() &&
                $cart->hasRecurringPayment() &&
                \array_key_exists( StripeApi::PRICING_PLAN_ATTRIBUTE_KEY, $gtAttributes )
            ) {
                $paymentDetails['local']['customer']    = [
                    'plan' => $gtAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY]
                ];
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
        
        if (
            ! isset( $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY] ) ||
            ! isset( $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY] )
        ) {
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
        if ( $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY] ) {
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
        
        $gateway        = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getFactoryName() );
        $stripeRequest  = new \ArrayObject([
            'customer'  => $gtAttributes[StripeApi::CUSTOMER_ATTRIBUTE_KEY],
            'items'     => [
                ['price' => $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]]
            ],
        ]);
        $gateway->execute( new CreateSubscription( $stripeRequest ) );
        
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
        $cart           = $this->orderFactory->getShoppingCart();
        //$payment        = $this->preparePayment( $cart );
        
        $gateway        = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getFactoryName() );
        $stripeRequest  = new \ArrayObject([
            "id"    => $gtAttributes[StripeApi::SUBSCRIPTION_ATTRIBUTE_KEY],
        ]);
        $gateway->execute( new CancelSubscription( $stripeRequest ) );
        
        $gtAttributes[StripeApi::PRICE_ATTRIBUTE_KEY]   = null;
        $subscription->setGatewayAttributes( $gtAttributes );
        $subscription->setRecurringPayment( false );
        
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
    }
}
