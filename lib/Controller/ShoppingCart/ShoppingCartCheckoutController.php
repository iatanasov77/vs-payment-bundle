<?php namespace Vankosoft\PaymentBundle\Controller\ShoppingCart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Component\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Form\PaymentForm;

class ShoppingCartCheckoutController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    /** @var Payment */
    protected $vsPayment;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $ordersRepository,
        Payment $vsPayment
    ) {
        $this->doctrine                 = $doctrine;
        $this->ordersRepository         = $ordersRepository;
        $this->vsPayment                = $vsPayment;
    }
    
    public function showPaymentMethodsFormAction( Request $request ): Response
    {
        $paymentDescription = $request->query->get( 'payment_description' );
        $form               = $this->createForm( PaymentForm::class );
        
        return $this->render( '@VSPayment/Pages/Payment/payment-form.html.twig', [
            'form'                  => $form->createView(),
            'paymentDescription'    => $paymentDescription ?: 'VankoSoft Payment',
        ]);
    }
    
    public function handlePaymentMethodsFormAction( Request $request ): Response
    {
        $cartId = $request->getSession()->get( OrderFactory::SESSION_BASKET_KEY );
        if ( ! $cartId ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in session !!!' );
        }
        $cart   = $this->ordersRepository->find( $cartId );
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart not exist in repository !!!' );
        }
        
        $form   = $this->createForm( PaymentForm::class );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em         = $this->doctrine->getManager();
            $formData   = $form->getData();
            
            $paymentMethod  = $formData['paymentMethod']['paymentMethod'];
            $cart->setPaymentMethod( $paymentMethod );
            $cart->setDescription( $formData['paymentDescription'] );
            $em->persist( $cart );
            $em->flush();
            
            $paymentPrepareUrl  = $this->vsPayment->getPaymentPrepareRoute( $paymentMethod->getGateway() );
            if( $request->isXmlHttpRequest() ) {
                return new JsonResponse([
                    'status'    => Status::STATUS_OK,
                    'data'      => [
                        'paymentPrepareUrl'  => $paymentPrepareUrl,
                    ]
                ]);
            } else {
                return $this->redirectToRoute( $paymentPrepareUrl );
            }
        }
    }
}