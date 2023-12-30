<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Sync;
use Payum\Core\Request\Cancel;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;
use Payum\Paypal\ExpressCheckout\Nvp\Api as PaypalApi;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

/*
 * 
 * Normal Payments
 * ================
 * https://github.com/Payum/Payum/blob/master/docs/symfony/custom-purchase-examples/paypal-express-checkout.md
 * 
 * Recurring Payments
 * ===================
 * https://github.com/Payum/Payum/blob/master/docs/paypal/express-checkout/recurring-payments-basics.md
 * https://github.com/Payum/Payum/blob/master/docs/paypal/express-checkout/cancel-recurring-payment.md
 * 
 * https://developer.paypal.com/api/nvp-soap/paypal-payments-standard/integration-guide/recurring-payments-dashboard/
 * 
 * DEPRECATED
 * ==========
 * PayPal Express Checkout is deprecated. Please, use new PayPal Commerce Platform integration.
 * PayPal Commerce Platform: https://github.com/Sylius/PayPalPlugin
 * 
 */
class PaypalExpressCheckoutController extends AbstractCheckoutRecurringController
{
    const AGREEMENT_CLASS           = 'Vankosoft\PaymentBundle\Model\PayPal\AgreementDetails';
    const RECURRING_PAYMENT_CLASS   = 'Vankosoft\PaymentBundle\Model\PayPal\RecurringPaymentDetails';
    
    public function prepareAction( Request $request ): Response
    {
        $cart           = $this->orderFactory->getShoppingCart();
        
        $subscriptions  = $cart->getSubscriptions();
        if ( ! empty( $subscriptions ) ) {
            $agreementAction    = 'Vankosoft\PaymentBundle\Controller\Checkout\PaypalExpressCheckoutController::createRecurringAgreementAction';
            
            $response           = $this->forward( $agreementAction, [
                'subscriptionId'    => $subscriptions[0]->getId(),
            ]);
            
            return $response;
        }
        
        $payment        = $this->preparePayment( $cart );
        $afterRoute     = 'vs_payment_paypal_express_checkout_done';
        $captureToken   = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment,
            $afterRoute
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function createRecurringAgreementAction( $subscriptionId, Request $request ): Response
    {
        $cart           = $this->orderFactory->getShoppingCart();
        
        $storage    = $this->payum->getStorage( self::AGREEMENT_CLASS );
        $agreement  = $storage->create();
        
        $agreement['PAYMENTREQUEST_0_AMT']              = 0; // For an initial amount to be charged please add it here, eg $10 setup fee
        $agreement['L_BILLINGTYPE0']                    = PaypalApi::BILLINGTYPE_RECURRING_PAYMENTS;
        $agreement['L_BILLINGAGREEMENTDESCRIPTION0']    = \substr( $cart->getDescription(), 0, 120 );
        $agreement['NOSHIPPING']                        = 1;
        
        $storage->update( $agreement );
        
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $afterRoute     = 'vs_payment_paypal_express_checkout_done';
        $captureToken   = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $agreement,
            'vs_payment_paypal_express_checkout_create_recurring_payment',
            ['subscriptionId' => $subscription->getId(),]
        );
        $storage->update( $agreement );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function createRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        $cart       = $this->orderFactory->getShoppingCart();
        
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        // you can invalidate the token. The url could not be requested any more.
        $this->payum->getHttpRequestVerifier()->invalidate( $token );
        
        $gateway = $this->payum->getGateway( $token->getGatewayName() );
        $gateway->execute( $agreementStatus = new GetHumanStatus( $token ) );
        
        if ( ! $agreementStatus->isCaptured() ) {
            // failure
            return $this->paymentFailed( $request, $agreementStatus );
        }
        
        $agreement          = $agreementStatus->getModel();
        $recurringPayment   = $this->prepareRecurringPayment( $cart, $agreement );
        
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $subscription->setRecurringPayment( true );
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
        
        $afterRoute = 'vs_payment_paypal_express_checkout_done';
        $captureToken   = $this->payum->getTokenFactory()->createToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $recurringPayment,
            $afterRoute
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function cancelRecurringPaymentAction( $paymentId, Request $request ): Response
    {
        $recurringPaymentClass  = self::RECURRING_PAYMENT_CLASS;
        $recurringPayment       = new $recurringPaymentClass;
        
        /** @var \Payum\Core\GatewayInterface $gateway */
        $gateway                = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getGatewayName() );
        $gateway->execute( new Cancel( $recurringPayment ) );
        $gateway->execute( new Sync( $recurringPayment ) );
        
        $gateway->execute( $status = new GetHumanStatus( $recurringPayment ) );
        
        if ( $status->isCanceled() ) {
            // yes it is cancelled
        } else {
            // hm... not yet. check other status isFailed and so on
        }
    }
    
    protected function preparePayment( OrderInterface $cart, $agreement = null )
    {
        $storage        = $this->payum->getStorage( $this->paymentClass );
        $payment        = $this->createPayment( $cart );
        
        // Payment Details
        $paymentDetails = $this->prepareOnetimePaymentDetails( $cart );
        $payment->setDetails( $paymentDetails );
        
        $this->doctrine->getManager()->persist( $cart );
        $this->doctrine->getManager()->flush();
        $storage->update( $payment );
        
        return $payment;
    }
    
    protected function prepareOnetimePaymentDetails( OrderInterface $cart ): array
    {
        $paymentDetails   = [
            'PAYMENTREQUEST_0_AMT'          => $cart->getTotalAmount() * 100,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $cart->getCurrencyCode(),
        ];
        
        return $paymentDetails;
    }
    
    protected function prepareRecurringPayment( OrderInterface $cart, $agreement ): \ArrayObject
    {
        $storage            = $this->payum->getStorage( self::RECURRING_PAYMENT_CLASS );
        $recurringPayment   = $storage->create();
        
        $recurringPayment   = [
            'TOKEN'             => $agreement['TOKEN'],
            'DESC'              => \substr( $cart->getDescription(), 0, 120 ),
            'EMAIL'             => $agreement['EMAIL'],
            'AMT'               => 0.05,
            'CURRENCYCODE'      => $cart->getCurrencyCode(),
            'BILLINGFREQUENCY'  => 7,
            'PROFILESTARTDATE'  => \date( DATE_ATOM ),
            'BILLINGPERIOD'     => PaypalApi::BILLINGPERIOD_DAY
        ];
        
        $gateway            = $this->payum->getGateway( $cart->getPaymentMethod()->getGateway()->getGatewayName() );
        $gateway->execute( new CreateRecurringPaymentProfile( $recurringPayment ) );
        $gateway->execute( new Sync( $recurringPayment ) );
        
        return $recurringPayment;
    }
    
    protected function createPayment( OrderInterface $cart )
    {
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() * 100 ); // Amount must convert to at least 100 stotinka.
        
        // Maximum length is 127 alphanumeric characters.
        $payment->setDescription( \substr( $cart->getDescription(), 0, 120 ) );
        
        $user   = $this->tokenStorage->getToken()->getUser();
        $payment->setClientId( $user ?$user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        return $payment;
    }
}
