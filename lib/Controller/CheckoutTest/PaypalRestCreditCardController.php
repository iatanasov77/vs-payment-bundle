<?php namespace Vankosoft\PaymentBundle\Controller\CheckoutTest;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Payum\Core\Payum;
use Payum\Core\Model\Payment;
use Payum\Core\Request\Capture;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Model\CreditCard;
use Payum\Core\Request\GetHttpRequest;


use PayPal\Api\Address;
use PayPal\Api\Amount;
use PayPal\Api\CreditCard as PayPalCreditCard;
use PayPal\Api\Payer;
use PayPal\Api\FundingInstrument;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;

use League\Uri\Http as HttpUri;
use League\Uri\UriModifier;


use Payum\Core\Request\Sync;
use Payum\Paypal\ExpressCheckout\Nvp\Request\Api\CreateRecurringPaymentProfile;
use Payum\Paypal\ExpressCheckout\Nvp\Api;



/**
 * USED REPOSITORY: paypal/rest-api-sdk-php (ABANDONED - SEE README)
 * =====================================================================
 * https://github.com/paypal/PayPal-PHP-SDK
 * 
 * 
 * USED MANUALS:
 * =============
 * https://github.com/Payum/Payum/blob/master/docs/paypal/rest/credit-card-purchase.md
 * 
 * 
 * ERRORS
 * =======
 * Got Http response code 401 when accessing https://api.sandbox.paypal.com/v1/oauth2/token.
 * 401 Unauthorized
 * 
 * Got Http response code 400 when accessing https://api.sandbox.paypal.com/v1/payments/payment.
 * 400 Bad Request
 * 
 * SHOULD BE
 * 201 Created
 * 
 */
final class PaypalRestCreditCardController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        /**
         * Payum Paypal Rest extension uses a model from the official Paypal's SDK lib
         * https://stackoverflow.com/questions/43868057/laravel-payum-and-paypal-rest
         */
        $paypalRestPaymentDetailsClass = 'Payum\Paypal\Rest\Model\PaymentDetails';
        //$paypalRestPaymentDetailsClass = $this->paymentClass;
        
        $storage = $this->payum->getStorage( $paypalRestPaymentDetailsClass );
        
        $payment = $storage->create();
        $storage->update($payment);
        
        $address = new Address();
        $address->line1 = "3909 Witmer Road";
        $address->line2 = "Niagara Falls";
        $address->city = "Niagara Falls";
        $address->state = "NY";
        $address->postal_code = "14305";
        $address->country_code = "US";
        $address->phone = "716-298-1822";
        
        
        //$card = new CreditCard();
        $card = new PayPalCreditCard();
        $card->type = "visa";
        $card->number = "4417119669820331";
        $card->expire_month = "12";
        $card->expire_year = "2022";
        $card->cvv2 = "012";
        $card->first_name = "Joe";
        $card->last_name = "Shopper";
        $card->billing_address = $address;
        
        $fi = new FundingInstrument();
        $fi->credit_card = $card;
        
        $payer = new Payer();
        $payer->payment_method = "credit_card";
        $payer->funding_instruments = array($fi);
        
        $amount = new Amount();
        $amount->currency = "USD";
        $amount->total = "1.00";
        
        $transaction = new Transaction();
        $transaction->amount = $amount;
        $transaction->description = "This is the payment description.";
        
        $payment->intent = "sale";
        $payment->payer = $payer;
        $payment->transactions = array($transaction);
        
        
        //$captureToken = $this->payum->getTokenFactory()->createCaptureToken( 'paypalRest', $payment, 'create_recurring_payment.php' );
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            'paypalRest',
            $payment,
            'wgp_paypal_cc_create' // the route to redirect after capture
        );
        
        $storage->update( $payment );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    public function createPaymentAction( Request $request ): Response
    {
        $token      = $this->payum->getHttpRequestVerifier()->verify( $request );
        $this->payum->getHttpRequestVerifier()->invalidate( $token );
        
        
        $gateway    = $this->payum->getGateway( $token->getGatewayName() );
        
        $agreementStatus = new GetHumanStatus( $token );
        $gateway->execute( $agreementStatus );
        
        if ( ! $agreementStatus->isCaptured() ) {
            header( 'HTTP/1.1 400 Bad Request', true, 400 );
            exit;
        }
        
        $agreement  = $agreementStatus->getModel();
        echo "<pre>"; var_dump( $agreement ); die;
        
        
        
        $recurringPaymentClass  = new \ArrayObject();
        $storage                = $this->payum->getStorage( $recurringPaymentClass );
        
        $recurringPayment = $storage->create();
        $recurringPayment['TOKEN'] = $agreement['TOKEN'];
        
        // Desc must match agreement 'L_BILLINGAGREEMENTDESCRIPTION' in prepare.php
        $recurringPayment['DESC'] = 'This is the payment description.';
        
        
        $recurringPayment['EMAIL'] = $agreement['EMAIL'];
        $recurringPayment['AMT'] = 1.00;
        $recurringPayment['CURRENCYCODE'] = 'USD';
        $recurringPayment['BILLINGFREQUENCY'] = 7;
        $recurringPayment['PROFILESTARTDATE'] = date( DATE_ATOM );
        $recurringPayment['BILLINGPERIOD'] = Api::BILLINGPERIOD_DAY;
        
        $gateway->execute( new CreateRecurringPaymentProfile( $recurringPayment ) );
        $gateway->execute( new Sync( $recurringPayment ) );
        
        $doneToken = $this->payum->getTokenFactory()->createToken( 'paypalRest', $recurringPayment, 'wgp_paypal_cc_done' );
        
        
        return $this->redirect( $doneToken->getTargetUrl() );
    }
}
