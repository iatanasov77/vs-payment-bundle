<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Form\CreditCardForm;

class CreditCardController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $ordersRepository
    ) {
        $this->doctrine         = $doctrine;
        $this->ordersRepository = $ordersRepository;
    }
    
    public function showCreditCardFormAction( $formAction, Request $request ): Response
    {
        $cartId = $request->getSession()->get( OrderFactory::SESSION_BASKET_KEY );
        if ( ! $cartId ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in session !!!' );
        }
        $cart   = $this->ordersRepository->find( $cartId );
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in repository !!!' );
        }
        
        $paymentMethod  = $cart->getPaymentMethod();
        $gatewayConfig  = (
                            $paymentMethod->getGateway()->getFactoryName() == 'stripe_checkout' || 
                            $paymentMethod->getGateway()->getFactoryName() == 'stripe_js'
                          ) ? $paymentMethod->getGateway()->getConfig() : '';
        
        $form           = $this->getCreditCardForm( base64_decode( $formAction ) );
        
        if( $request->isXmlHttpRequest() ) {
            
            /*
            return $this->render( '@VSPayment/Pages/CreditCard/Partial/StripeJsV3Form.html.twig', [
                'form'          => $form->createView(),
                'paymentMethod' => $paymentMethod,
                'captureKey'    => $gatewayConfig['publishable_key'],
                'formAction'    => '',
                'formMethod'    => 'POST',
            ]);
            */
            return $this->render( '@VSPayment/Pages/CreditCard/Partial/CreditCardForm.html.twig', [
                'form'          => $form->createView(),
                'paymentMethod' => $paymentMethod,
                'captureKey'    => $gatewayConfig['publishable_key'],
                'formAction'    => '',
                'formMethod'    => 'POST',
            ]);
            
        } else {
            return $this->render( '@VSPayment/Pages/CreditCard/credit_card.html.twig', [
                'form'          => $form->createView(),
                'paymentMethod' => $paymentMethod,
                'captureKey'    => $gatewayConfig['publishable_key'],
            ]);
        }
    }
    
    protected function getCreditCardForm( $captureUrl )
    {
        return $this->createForm( CreditCardForm::class, [
            'captureUrl' => $captureUrl,
        ]);
    }
}
