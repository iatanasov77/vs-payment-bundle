<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Payum\Stripe\Request\Api\CreatePlan;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
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
    
    public function createRecurringPaymentAction( $packagePlanId, Request $request ): Response
    {
        throw new HttpException( 'Not Needed and Not Implemented !!!' );
    }
    
    public function cancelAction( $paymentId, Request $request ): Response
    {
        // $paymentDetails['local']['customer']['subscriptions']['data'][0]['id']
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
        
        $gateway        = $cart->getPaymentMethod()->getGateway();
        $pricingPlan    = $cart->getItems()->first()->getPaidServiceSubscription()->getPricingPlan();
        $gtAttributes   = $pricingPlan->getGatewayAttributes();
        
        if ( $gateway->getSupportRecurring() && \array_key_exists( StripeApi::PRICING_PLAN_ATTRIBUTE_KEY, $gtAttributes ) ) {
            $paymentDetails['local']['customer']    = [
                'plan' => $gtAttributes[StripeApi::PRICING_PLAN_ATTRIBUTE_KEY]
            ];
        }
        
        return $paymentDetails;
    }
}
