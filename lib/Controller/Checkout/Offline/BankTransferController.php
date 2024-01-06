<?php namespace Vankosoft\PaymentBundle\Controller\Checkout\Offline;

use Vankosoft\PaymentBundle\Controller\AbstractCheckoutOfflineController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Payum\Offline\Constants as PayumOfflineConstants;

class BankTransferController extends AbstractCheckoutOfflineController
{
    public function getInfo( Request $request ): Response
    {
        $cart           = $this->orderFactory->getShoppingCart();
        $gatewayConfig  = $cart->getPaymentMethod()->getGateway()->getConfig();
        
        return new JsonResponse([
            'config'    => $gatewayConfig,
        ]);
    }
    
    public function prepareAction( Request $request ): Response
    {
        $cart   = $this->orderFactory->getShoppingCart();
        //$this->debugGateway( $cart->getPaymentMethod()->getGateway() );
        
        $storage = $this->payum->getStorage( $this->paymentClass );
        $payment = $storage->create();
        
        $payment->setOrder( $cart );
        $payment->setNumber( uniqid() );
        $payment->setCurrencyCode( $cart->getCurrencyCode() );
        $payment->setRealAmount( $cart->getTotalAmount() ); // Need this for Real (Human Readable) Amount.
        $payment->setTotalAmount( $cart->getTotalAmount() );
        $payment->setDescription( $cart->getDescription() );
        
        $user   = $this->securityBridge->getUser();
        $payment->setClientId( $user ? $user->getId() : 'UNREGISTERED_USER' );
        $payment->setClientEmail( $user ? $user->getEmail() : 'UNREGISTERED_USER' );
        
        // Set Payment Not Paid 
        $payment->setDetails( [PayumOfflineConstants::FIELD_PAID => false] );

        $storage->update($payment);

        $captureToken   = $this->payum->getTokenFactory()->createCaptureToken(
            $cart->getPaymentMethod()->getGateway()->getGatewayName(),
            $payment, 
            'vs_payment_offline_bank_transfer_done' // the route to redirect after capture
        );

        return $this->redirect( $captureToken->getTargetUrl() );    
    }
    
    public function debugGateway( $gateway )
    {
        var_dump( $this->payum->getGateway( $gateway->getGatewayName() ) ); die;
        var_dump( $gateway ); die;
    }
}
