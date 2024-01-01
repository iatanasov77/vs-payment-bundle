<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Sync;
use Payum\Core\Request\Cancel;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;
use Payum\Paypal\ExpressCheckout\Nvp\Api as PaypalApi;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Controller\AbstractCheckoutRecurringController;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanSubscriptionInterface;

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
 * PAYPAL NVP/SOAP API
 * ===================
 * https://developer.paypal.com/api/nvp-soap/create-recurring-payments-profile-nvp/
 * 
 * PAYPAL REST API
 * ===============
 * https://developer.paypal.com/docs/api/catalog-products/v1/
 * https://developer.paypal.com/docs/api/subscriptions/v1/
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
        if ( $cart->hasRecurringPayment() && ! empty( $subscriptions ) ) {
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
        $cart               = $this->orderFactory->getShoppingCart();
        $paymentMethod      = $cart->getPaymentMethod();
        
        $storagePayment     = $this->payum->getStorage( $this->paymentClass );
        $storageAgreement   = $this->payum->getStorage( self::AGREEMENT_CLASS );
        $agreement          = $this->prepareRecurringAgreement( $cart );
        
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $gatewayName    = $paymentMethod ? $paymentMethod->getGateway()->getGatewayName() : $subscription->getGatewayFactory();
        
        $afterRoute     = 'vs_payment_paypal_express_checkout_create_recurring_payment';
        $captureToken   = $this->payum->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $agreement['payment'],
            $afterRoute,
            ['subscriptionId' => $subscription->getId(),]
        );
        
        $storagePayment->update( $agreement['payment'] );
        $storageAgreement->update( $agreement['agreement'] );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function createRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        $cart       = $this->orderFactory->getShoppingCart();
        
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        // you can invalidate the token. The url could not be requested any more.
        $this->payum->getHttpRequestVerifier()->invalidate( $token );
        
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        $gateway->execute( $agreementStatus = new GetHumanStatus( $token ) );
        
        $response   = $this->onRecurringAggreementStatus( $agreementStatus );
        if ( $response ) {
            return $response;
        }
        
        $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
        $subscription->setRecurringPayment( true );
        $this->doctrine->getManager()->persist( $subscription );
        $this->doctrine->getManager()->flush();
        
        $agreement          = $agreementStatus->getModel();
        $recurringPayment   = $this->prepareRecurringPayment( $cart, $agreement, $subscription );
        
        $afterRoute = 'vs_payment_paypal_express_checkout_done';
        $captureToken   = $this->payum->getTokenFactory()->createToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $recurringPayment,
            $afterRoute
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function cancelRecurringPaymentAction( $subscriptionId, Request $request ): Response
    {
        $subscription           = $this->subscriptionsRepository->find( $subscriptionId );
        $payment                = $subscription->getOrderItem()->getOrder()->getPayment();
        $recurringPaymentClass  = self::RECURRING_PAYMENT_CLASS;
        $recurringPayment       = new $recurringPaymentClass( $payment->getDetails() );
        
        /** @var \Payum\Core\GatewayInterface $gateway */
        $gateway                = $this->payum->getGateway( $subscription->getGateway()->getGatewayName() );
        $gateway->execute( new Cancel( $recurringPayment ) );
        $gateway->execute( new Sync( $recurringPayment ) );
        
        $gateway->execute( $status = new GetHumanStatus( $recurringPayment ) );
        
        if ( $status->isCanceled() ) {
            // yes it is cancelled
            $subscription->setRecurringPayment( false );
            $this->doctrine->getManager()->persist( $subscription );
            $this->doctrine->getManager()->flush();
            
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_cancel_subscription_recurring_success', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        } else {
            // hm... not yet. check other status isFailed and so on
            $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_cancel_subscription_recurring_error', [], 'VSPaymentBundle' );
            $request->getSession()->getFlashBag()->add( 'error', $flashMessage );
        }
        
        $redirectRoute  = $this->routeRedirectOnPricingPlanDone ? $this->routeRedirectOnPricingPlanDone : 'vs_payment_pricing_plans';
        if ( $redirectRoute && $request->isXmlHttpRequest() ) {
            return $this->jsonResponse( Status::STATUS_ERROR, $redirectRoute );
        } else {
            return $this->redirectToRoute( $redirectRoute );
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
    
    protected function prepareRecurringAgreement( OrderInterface $cart )
    {
        //return $this->prepareRecurringAgreementDetails( $cart );
        
        $storage        = $this->payum->getStorage( $this->paymentClass );
        $payment        = $this->createPayment( $cart );
        
        // Payment Details
        $agreementDetails = $this->prepareRecurringAgreementDetails( $cart );
        $payment->setDetails( $agreementDetails->getArrayCopy() );
        
        $storage->update( $payment );
        
        return [
            'payment'   => $payment,
            'agreement' => $agreementDetails,
        ];
    }
    
    protected function prepareRecurringAgreementDetails( OrderInterface $cart ): \ArrayObject
    {
        $storage    = $this->payum->getStorage( self::AGREEMENT_CLASS );
        $agreement  = $storage->create();
        
        //$agreement['PAYMENTREQUEST_0_AMT']              = 0; // For an initial amount to be charged please add it here, eg $10 setup fee
        $agreement['PAYMENTREQUEST_0_AMT']              = $cart->getTotalAmount();
        $agreement['PAYMENTREQUEST_0_CURRENCYCODE']     = $cart->getCurrencyCode();
        
        $agreement['L_BILLINGTYPE0']                    = PaypalApi::BILLINGTYPE_RECURRING_PAYMENTS;
        $agreement['L_BILLINGAGREEMENTDESCRIPTION0']    = \substr( $cart->getDescription(), 0, 120 );
        $agreement['NOSHIPPING']                        = 1;
        
        $storage->update( $agreement );
        
        return $agreement;
    }
    
    protected function prepareRecurringPayment( OrderInterface $cart, $agreement, $subscription )
    {
        //return $this->prepareRecurringPaymentDetails( $cart, $agreement, $subscription );
        
        $storage        = $this->payum->getStorage( $this->paymentClass );
        $payment        = $this->createPayment( $cart );
        
        // Payment Details
        $paymentDetails = $this->prepareRecurringPaymentDetails( $cart, $agreement, $subscription );
        $payment->setDetails( $paymentDetails->getArrayCopy() );
        
        $this->doctrine->getManager()->persist( $cart );
        $this->doctrine->getManager()->flush();
        $storage->update( $payment );
        
        return $payment;
    }
    
    protected function prepareRecurringPaymentDetails( OrderInterface $cart, $agreement, $subscription ): \ArrayObject
    {
        $user                   = $this->tokenStorage->getToken()->getUser();
        $previousSubscription   = $user->getActivePricingPlanSubscriptionByService(
            $subscription->getPricingPlan()->getPaidService()->getPayedService()
        );
        
        $storage            = $this->payum->getStorage( self::RECURRING_PAYMENT_CLASS );
        $recurringPayment   = $storage->create();
        
        $recurringPayment['TOKEN']              = $agreement['TOKEN'];
        $recurringPayment['EMAIL']              = $agreement['EMAIL'];
        
        // Desc must match agreement 'L_BILLINGAGREEMENTDESCRIPTION' in prepare.php
        $recurringPayment['DESC']               = \substr( $cart->getDescription(), 0, 120 );
        
        $recurringPayment['AMT']                = $cart->getTotalAmount();
        $recurringPayment['CURRENCYCODE']       = $cart->getCurrencyCode();
        
        // MANUAL: https://developer.paypal.com/api/nvp-soap/create-recurring-payments-profile-nvp/
        $startDate          = $previousSubscription && $previousSubscription->isPaid() ?
                                $previousSubscription->getExpiresAt() :
                                new \DateTime();
        $recurringPayment['PROFILESTARTDATE']   = $startDate->format( \DateTime::ATOM );
        
        $paidServicePeriod  = $subscription->getPricingPlan()->getPaidService()->getSubscriptionPeriod();
        $billingCycle       = $this->vsPayment->getPaypalNvpBillingCycle( $paidServicePeriod );
        $recurringPayment['BILLINGPERIOD']      = $billingCycle['period'];
        $recurringPayment['BILLINGFREQUENCY']   = $billingCycle['frequency'];
        
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
    
    private function onRecurringAggreementStatus( GetHumanStatus $agreementStatus ): ?Response
    {
        // var_dump( $agreementStatus->getValue() ); die;
        
        if ( $agreementStatus->isCaptured() ) {
            // return $this->paymentFailed( $request, $agreementStatus );
        }
        
        if ( $agreementStatus->isPending() ) {
            // return $this->paymentFailed( $request, $agreementStatus );
        }
        
        if ( $agreementStatus->isFailed() ) {
            // failure
            return $this->paymentFailed( $request, $agreementStatus );
        }
        
        return null;
    }
}
