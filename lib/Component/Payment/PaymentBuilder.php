<?php namespace Vankosoft\PaymentBundle\Component\Payment;

use Payum\Core\Payum;
use Payum\Core\Request\GetCurrency;

use Vankosoft\PaymentBundle\Entity\Agreement;
use Vankosoft\PaymentBundle\Entity\Payment;
use Vankosoft\PaymentBundle\Entity\GatewayConfig;

/*
 * When execute action Payum\Paypal\ExpressCheckout\Nvp\Action\ConvertPaymentAction
 * price is divided by divisor
 *
 * zaradi tova prawq integer umnojen po divisor
 */

class PaymentBuilder
{
    private $payum;
    private $storage;
    private $currencyDivisor;
    
    public function __construct( Payum $payum )
    {
        $this->payum   = $payum;
    }
    
    public function updateStorage( $model )
    {
        $this->storage->update( $model );
    }
    
    public function buildAgreement( $user, $packagePlan )
    {
        $this->storage = $this->payum->getStorage( Agreement::class );
        
        /** @var $agreement AgreementDetails */
        $agreement = $this->storage->create();
        
        $agreement->setPackagePlan( $packagePlan );
        
        return $agreement;
    }
    
    public function buildPayment( $user, $packagePlan, $gatewayName )
    {
        $this->storage = $this->payum->getStorage( Payment::class );
        
        $payment    = $this->storage->create();
        $divisor    = $this->getCurrencyDivisor( $packagePlan->getCurrency(), $gatewayName );
        
        $payment->setPackagePlan( $packagePlan );
        $payment->setCurrencyDivisor( $divisor );
        
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $packagePlan->getCurrency() );
        $payment->setTotalAmount( $packagePlan->getPrice() * $divisor );
        $payment->setDescription( $packagePlan->getCurrency() );
        $payment->setClientId( $user->getId() );
        $payment->setClientEmail( $user->getEmail() );
        
        return $payment;
    }
    
    private function getCurrencyDivisor( $currencyCode, $gatewayName )
    {
        if ( ! $this->currencyDivisor ) {
            $this->payum->getGateway( $gatewayName )->execute( $currency = new GetCurrency( $currencyCode ) );
            $this->currencyDivisor  = pow( 10, $currency->exp );
        }

        return $this->currencyDivisor;        
    }
}
