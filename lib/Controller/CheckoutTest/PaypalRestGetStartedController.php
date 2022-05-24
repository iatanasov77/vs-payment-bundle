<?php namespace Vankosoft\PaymentBundle\Controller\CheckoutTest;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;



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
 */
final class PaypalRestGetStartedController extends AbstractCheckoutController
{
    public function prepareAction( Request $request ): Response
    {
        $paypalRestPaymentDetailsClass = 'Payum\Paypal\Rest\Model\PaymentDetails';
        //$paypalRestPaymentDetailsClass = $this->paymentClass;
        
        $storage = $this->payum->getStorage( $paypalRestPaymentDetailsClass );
        
        $payment = $storage->create();
        $storage->update($payment);
        
        $payer = new Payer();
        $payer->payment_method = "paypal";
        
        $amount = new Amount();
        $amount->currency = "USD";
        $amount->total = "1.00";
        
        $transaction = new Transaction();
        $transaction->amount = $amount;
        $transaction->description = "This is the payment description.";
        
        $captureToken = $this->payum->getTokenFactory()->createCaptureToken(
            'paypalRest',
            $payment,
            'wgp_paypal_gs_done' // the route to redirect after capture
        );
        
        $redirectUrls = new RedirectUrls();
        $redirectUrls->return_url = $captureToken->getTargetUrl();
        $redirectUrls->cancel_url = (string) UriModifier::mergeQuery(HttpUri::createFromString($captureToken->getTargetUrl()), 'cancelled=1');
        
        $payment->intent = "sale";
        $payment->payer = $payer;
        $payment->redirect_urls = $redirectUrls;
        $payment->transactions = array($transaction);
        
        $storage->update($payment);
        
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
}
