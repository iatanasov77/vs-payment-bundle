<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Exception\ShoppingCardException;
use Vankosoft\PaymentBundle\Form\PaymentForm;
use Vankosoft\PaymentBundle\Form\CreditCardForm;

class PaymentController extends AbstractController
{
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var EntityRepository */
    protected $ordersRepository;
    
    /** @var EntityRepository */
    protected $payableObjectsRepository;
    
    public function __construct(
        Factory $ordersFactory,
        Factory $orderItemsFactory,
        EntityRepository $ordersRepository,
        EntityRepository $payableObjectsRepository
    ) {
        $this->ordersFactory            = $ordersFactory;
        $this->orderItemsFactory        = $orderItemsFactory;
        $this->ordersRepository         = $ordersRepository;
        $this->payableObjectsRepository = $payableObjectsRepository;
    }
    
    public function addToCardAction( $payableObjectId, Request $request ): Response
    {
        $cardId = $this->get('session')->get( 'vs_payment_basket_id' );
        $card   = $cardId ? $this->ordersRepository->find( $cardId ) : $this->createCard();
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card cannot be created !!!' );
        }
        $em             = $this->getDoctrine()->getManager();
        
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
        $cardId = $this->get('session')->get( 'vs_payment_basket_id' );
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
            $em         = $this->getDoctrine()->getManager();
            $formData   = $form->getData();
            
            $card->setPaymentMethod( $formData['paymentMethod'] );
            $card->setDescription( $formData['paymentDescription'] );
            $em->persist( $card );
            $em->flush();
            
            $paymentPrepareUrl  = $formData['paymentMethod']->getPaymentRoute();
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'data'      => [
                    'paymentPrepareUrl'  => $paymentPrepareUrl ?: 'not_configured',
                ]
            ]);
        }
    }
    
    public function showCreditCardFormAction( $formAction, Request $request ): Response
    {
        $cardId = $this->get('session')->get( 'vs_payment_basket_id' );
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
    
    protected function createCard()
    {
        $em     = $this->getDoctrine()->getManager();
        $card  = $this->ordersFactory->createNew();
        
        $card->setUser( $this->getUser() );
        
        $em->persist( $card );
        $em->flush();
        
        $this->get('session')->set( 'vs_payment_basket_id', $card->getId() );
        return $card;
    }
    
    protected function getCreditCardForm( $captureUrl )
    {
        return $this->createForm( CreditCardForm::class, [
            'captureUrl' => $captureUrl,
        ]);
    }
}
