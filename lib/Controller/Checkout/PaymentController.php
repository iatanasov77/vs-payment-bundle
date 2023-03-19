<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Exception\ShoppingCardException;
use Vankosoft\PaymentBundle\Form\PaymentForm;
use Vankosoft\PaymentBundle\Form\CreditCardForm;

class PaymentController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Payment */
    protected $vsPayment;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    /** @var EntityRepository */
    protected $payableObjectsRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Payment $vsPayment,
        Factory $ordersFactory,
        Factory $orderItemsFactory,
        EntityRepository $ordersRepository,
        EntityRepository $payableObjectsRepository
    ) {
        $this->doctrine                 = $doctrine;
        $this->securityBridge           = $securityBridge;
        $this->vsPayment                = $vsPayment;
        $this->ordersFactory            = $ordersFactory;
        $this->orderItemsFactory        = $orderItemsFactory;
        $this->ordersRepository         = $ordersRepository;
        $this->payableObjectsRepository = $payableObjectsRepository;
    }
    
    public function addToCardAction( $payableObjectId, Request $request ): Response
    {
        $cardId = $request->getSession()->get( 'vs_payment_basket_id' );
        $card   = $cardId ? $this->ordersRepository->find( $cardId ) : $this->createCard( $request );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card cannot be created !!!' );
        }
        $em             = $this->doctrine->getManager();
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $payableObject  = $this->payableObjectsRepository->find( $payableObjectId );
        
        $orderItem->setObject( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $card->addItem( $orderItem );
        $em->persist( $card );
        $em->flush();
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
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
        $cardId = $request->getSession()->get( 'vs_payment_basket_id' );
        if ( ! $cardId ) {
            throw new ShoppingCardException( 'Card not exist in session !!!' );
        }
        $card   = $this->ordersRepository->find( $cardId );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card not exist in repository !!!' );
        }
        
        $form   = $this->createForm( PaymentForm::class );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em         = $this->doctrine->getManager();
            $formData   = $form->getData();
            
            $card->setPaymentMethod( $formData['paymentMethod'] );
            $card->setDescription( $formData['paymentDescription'] );
            $em->persist( $card );
            $em->flush();
            
            $paymentPrepareUrl  = $this->vsPayment->getPaymentPrepareRoute( $formData['paymentMethod']->getGateway() );
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'data'      => [
                    'paymentPrepareUrl'  => $paymentPrepareUrl,
                ]
            ]);
        }
    }
    
    public function showCreditCardFormAction( $formAction, Request $request ): Response
    {
        $cardId = $request->getSession()->get( 'vs_payment_basket_id' );
        if ( ! $cardId ) {
            throw new ShoppingCardException( 'Card not exist in session !!!' );
        }
        $card   = $this->ordersRepository->find( $cardId );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card not exist in repository !!!' );
        }
        
        $form   = $this->getCreditCardForm( base64_decode( $formAction ) );
        
        return $this->render( '@VSPayment/Pages/CreditCard/credit_card.html.twig', [
            'form'          => $form->createView(),
            'paymentMethod' => $card->getPaymentMethod(),
        ]);
    }
    
    protected function createCard( Request $request )
    {
        $session = $request->getSession();
        $session->start();  // Ensure Session is Started
        
        $em    = $this->doctrine->getManager();
        $card  = $this->ordersFactory->createNew();
        
        $card->setUser( $this->securityBridge->getUser() );
        $card->setSessionId( $session->getId() );
        
        $em->persist( $card );
        $em->flush();
        
        $request->getSession()->set( 'vs_payment_basket_id', $card->getId() );
        return $card;
    }
    
    protected function getCreditCardForm( $captureUrl )
    {
        return $this->createForm( CreditCardForm::class, [
            'captureUrl' => $captureUrl,
        ]);
    }
}
