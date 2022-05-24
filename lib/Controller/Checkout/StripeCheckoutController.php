<?php  namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;

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
 * OmnipayBridge is Very Old
 * ==========================
 * https://github.com/Payum/OmnipayBridge/blob/master/composer.json
 */
/*
 * TEST MAIL: i.atanasov77@gmail.com
 *
 * TEST CARDS
 * ===========
 
 Card Type  |	Card Number	    |  Exp. Date  | CVV Code
 --------------------------------------------------------
 Visa       | 4242424242424242  |   Any future| Any 3
 |                   |      date   | digits
 ---------------------------------------------------------
 Visa       | 4263982640269299  |   02/2023   |  837
 --------------------------------------------------------
 Visa       | 4263982640269299  |   04/2023   |  738
 
 */
class StripeCheckoutController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        $em     = $this->getDoctrine()->getManager();
        $card   = $this->getShoppingCard();
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $card->getCurrencyCode() );
        $payment->setTotalAmount( $card->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        $payment->setDescription( $card->getDescription() );
        
        $payment->setClientId( $this->getUser() ? $this->getUser()->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $this->getUser() ? $this->getUser()->getEmail() : 'UNREGISTERED_USER' );
        
        /*
         * Stripe. Store credit card and use later.
         * ====================================================================================
         * https://github.com/Payum/Payum/blob/master/docs/stripe/store-card-and-use-later.md
         */
        $payment->setDetails([
            'local' => [
                'save_card' => true,
            ]
        ]);
        
        $payment->setOrder( $card );
        $em->persist( $card );
        $em->flush();
        $storage->update( $payment );
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            $card->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            'vs_payment_stripe_checkout_done' // the route to redirect after capture
        );
        
        
        if ( $card->getPaymentMethod()->getGateway()->getFactoryName() == 'stripe_js' ) {
            $captureUrl = base64_encode( $captureToken->getTargetUrl() );
            return $this->redirect( $this->generateUrl( 'vs_payment_show_credit_card_form', ['formAction' => $captureUrl] ) );
        } else {
            return $this->redirect( $captureToken->getTargetUrl() );
        }
    }
}
