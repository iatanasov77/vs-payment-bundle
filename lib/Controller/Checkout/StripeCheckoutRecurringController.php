<?php  namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Payum\Stripe\Request\Api\CreatePlan;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

/**
 * USED MANUALS:
 * =============
 * https://stackoverflow.com/questions/34908805/create-a-recurring-or-subscription-payment-using-payum-stripe-on-symfony-2
 * https://github.com/Payum/Payum/blob/master/docs/stripe/subscription-billing.md
 * https://github.com/Payum/PayumBundle
 * 
 * https://github.com/Payum/Payum/blob/master/docs/stripe/store-card-and-use-later.md
 * 
 */
class StripeCheckoutRecurringController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        $em     = $this->doctrine->getManager();
        $cart   = $this->getShoppingCart( $request );
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setTotalAmount( $cart->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->tokenStorage->getToken()->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        $paymentDetails   = [
            'local' => []
        ];
        
        if ( $cart->hasRecurringPayment() ) {
            $plan   = $this->createSubscriptionPlan( $cart );
            
            /** @var \Payum\Core\Payum $payum */
            $gateway    = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getGatewayName() );
            $gateway->execute( new CreatePlan( $plan ) );
            
            $paymentDetails['local']['customer']    = ['plan' => $plan['id']];
        }
        
        /*
         * Stripe. Store credit card and use later.
         * ====================================================================================
         * https://github.com/Payum/Payum/blob/master/docs/stripe/store-card-and-use-later.md
         */
        $paymentDetails['local']['save_card']   = true;
        $payment->setDetails( $paymentDetails );
        
        $payment->setOrder( $cart );
        $em->persist( $cart );
        $em->flush();
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
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
    
    protected function createSubscriptionPlan( OrderInterface $order )
    {
        $pricingPlan        = $order->getItems()->first()->getPaidServiceSubscription();
        
        /*
        $subscriptionPlan   = new \ArrayObject([
            "amount"    => $order->getTotalAmount(),
            "interval"  => "month",
            "name"      => "Amazing Gold Plan",
            "currency"  => $order->getCurrencyCode(),
            "id"        => "gold"
        ]);
        */
        
        $subscriptionPlan   = new \ArrayObject([
            "amount"    => $order->getTotalAmount(),
            "interval"  => "month",
            "name"      => "My Gold Plan",
            "currency"  => $order->getCurrencyCode(),
            "id"        => "my-gold"
        ]);
        
        return $subscriptionPlan;
    }
}
