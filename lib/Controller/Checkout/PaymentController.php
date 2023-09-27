<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Vankosoft\PaymentBundle\Controller\BaseShoppingCartController;
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
use Vankosoft\PaymentBundle\Component\PayableObject;
use Vankosoft\PaymentBundle\Form\SelectPricingPlanForm;

class PaymentController extends BaseShoppingCartController
{
    /** @var Payment */
    protected $vsPayment;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var EntityRepository */
    protected $productsRepository;
    
    /** @var EntityRepository */
    protected $pricingPlansRepository;
    
    /** @var EntityRepository */
    protected $paymentMethodsRepository;
    
    /** @var EntityRepository */
    protected $payableObjectsRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Payment $vsPayment,
        Factory $ordersFactory,
        Factory $orderItemsFactory,
        EntityRepository $ordersRepository,
        EntityRepository $productsRepository,
        EntityRepository $pricingPlansRepository,
        EntityRepository $paymentMethodsRepository,
        EntityRepository $payableObjectsRepository
    ) {
        parent::__construct( $doctrine, $securityBridge, $ordersFactory, $ordersRepository );
        
        $this->vsPayment                = $vsPayment;
        $this->orderItemsFactory        = $orderItemsFactory;
        $this->productsRepository       = $productsRepository;
        $this->pricingPlansRepository   = $pricingPlansRepository;
        $this->paymentMethodsRepository = $paymentMethodsRepository;
        $this->payableObjectsRepository = $payableObjectsRepository;
    }
    
    public function addToCardAction( $payableObjectType, $payableObjectId, $qty, Request $request ): Response
    {
        $cardId = $request->getSession()->get( 'vs_payment_basket_id' );
        $card   = $cardId ? $this->ordersRepository->find( $cardId ) : $this->createCard( $request );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card cannot be created !!!' );
        }
        
        switch ( $payableObjectType ) {
            case PayableObject::OBJECT_TYPE_PRICING_PLAN:
                $this->addPricingPlanToCard( $payableObjectId, $card );
                break;
            case PayableObject::OBJECT_TYPE_PRODUCT:
                $this->addProductToCard( $payableObjectId, $qty, $card );
                break;
            case PayableObject::OBJECT_TYPE_SERVICE:
                $this->addServiceToCard( $payableObjectId, $qty, $card );
                break;
            default:
                throw new ShoppingCardException( 'Invalid Payable Object Type !!!' );
        }
        
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
    
    public function handlePricingPlanFormAction( Request $request ): Response
    {
        $card   = $this->createCard( $request );
        if ( ! $card ) {
            throw new ShoppingCardException( 'Card cannot be created !!!' );
        }
        
        $form   = $this->createForm( SelectPricingPlanForm::class );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em             = $this->doctrine->getManager();
            $formData       = $form->getData();
            $pricingPlan    = $this->addPricingPlanToCard( $formData['pricingPlan'], $card );
            $paymentMethod  = $this->paymentMethodsRepository->find( $formData['paymentMethod'] );
            
            $card->setPaymentMethod( $paymentMethod );
            $card->setDescription( $pricingPlan->getDescription() );
            $em->persist( $card );
            $em->flush();
            
            $paymentPrepareUrl  = $this->vsPayment->getPaymentPrepareRoute( $paymentMethod->getGateway() );
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
    
    protected function getCreditCardForm( $captureUrl )
    {
        return $this->createForm( CreditCardForm::class, [
            'captureUrl' => $captureUrl,
        ]);
    }
    
    protected function addServiceToCard( $payableObjectId, $qty, $card ): void
    {
        $em             = $this->doctrine->getManager();
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $payableObject  = $this->payableObjectsRepository->find( $payableObjectId );
        
        $orderItem->setPaidServiceSubscription( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $card->addItem( $orderItem );
        $em->persist( $card );
        $em->flush();
    }
    
    protected function addProductToCard( $payableObjectId, $qty, $card ): void
    {
        $em             = $this->doctrine->getManager();
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $payableObject  = $this->productsRepository->find( $payableObjectId );
        
        $orderItem->setProduct( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $card->addItem( $orderItem );
        $em->persist( $card );
        $em->flush();
    }
    
    protected function addPricingPlanToCard( $pricingPlanId, &$card )
    {
        $em             = $this->doctrine->getManager();
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $pricingPlan    = $this->pricingPlansRepository->find( $pricingPlanId );
        $payableObject  = $pricingPlan->getPaidServicePeriod();
        
        $orderItem->setPaidServiceSubscription( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $card->addItem( $orderItem );
        $em->persist( $card );
        $em->flush();
        
        return $pricingPlan;
    }
}
