<?php  namespace IA\PaymentBundle\Controller\PaymentMethod;

use Symfony\Component\HttpFoundation\Request;

class StripeCheckoutController extends AbstractPaymentMethodController
{
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
    
    protected function createPlan()
    {
        $product = new \ArrayObject([
            "id"    => "plan_subscription_product",
            "name"  => "plan_subscription_product",
            "type"  => 'service',
        ]);
        
        $plan = new \ArrayObject([
            "id" => "monthly_subscription_plan",
            "amount" => 2000,
            "interval" => "month",
            "currency" => "usd",
            "product" => 'plan_subscription_product'
        ]);
        
        $gw = $this->getPayum()->getGateway( $this->gatewayName() );
        
        try {
            $pr = $gw->execute( new \Payum\Stripe\Request\Api\CreateProduct( $product ) );
            if ( isset( $product['error'] ) ) {
                //throw new \Exception( $product['error']['message'] );
            }
        } catch ( Exception $e ) {
            // throw new \Exception( $e->getMessage() );
        }
        
        try {
            $pl = $gw->execute( new \Payum\Stripe\Request\Api\CreatePlan( $plan ) );
            if ( isset( $plan['error'] ) ) {
                throw new \Exception( $plan['error']['message'] );
            }
        } catch (Exception $e) {
            // throw new \Exception( $e->getMessage() );
        }
        
        return $plan;
    }
    
    public function prepareAction( Request $request )
    {
        $ppr            = $this->getDoctrine()->getRepository( 'IAUsersBundle:PackagePlan' );
        
        $packagePlan    = $ppr->find( $request->query->get( 'packagePlanId' ) );
        if ( ! $packagePlan ) {
            throw new \Exception('Invalid Request!!!');
        }
        
        $plan       = $this->createPlan();
        
        $pb         = $this->get( 'ia_payment_builder' );
        $payment    = $pb->buildPayment( $this->getUser(), $packagePlan, $this->gatewayName() );
        
        $payment->setPaymentMethod( 'stripe' );
        $payment->setDetails([
            'currency'      => $packagePlan->getCurrency(),
            'amount'        => $packagePlan->getPrice() * $payment->getCurrencyDivisor(),
            'description'   => $packagePlan->getDescription(),
            'local'         => [
                'save_card' => true,
                'customer' => [
                    'plan' => $plan['id']    // $packagePlan->getId()
                ],
            ]
        ]);
        $pb->updateStorage( $payment );
        
        $captureToken = $this->get( 'payum' )->getTokenFactory()->createCaptureToken(
            $this->gatewayName(),
            $payment,
            'ia_payment_stripe_checkout_done' // the route to redirect after capture;
        );
        
        return $this->redirect( $captureToken->getTargetUrl() );
    }
    
    protected function gatewayName()
    {
        return 'stripe_checkout_gateway';
    }
    
    protected function getErrorMessage( $details )
    {
        return 'STRIPE ERROR: ' . $details['error']['message'];
    }
}
