<?php namespace Vankosoft\PaymentBundle\Controller\PricingPlans;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Component\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Component\Exception\CheckoutException;
use Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface;
use Vankosoft\PaymentBundle\Form\SelectPricingPlanForm;
use Vankosoft\PaymentBundle\EventSubscriber\Event\CreateSubscriptionEvent;

class PricingPlanCheckoutController extends AbstractController
{
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var EventDispatcherInterface */
    protected $eventDispatcher;
    
    /** @var SecurityBridge */
    protected $securityBridge;
    
    /** @var Factory */
    protected $ordersFactory;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    /** @var Factory */
    protected $orderItemsFactory;
    
    /** @var RepositoryInterface */
    protected $pricingPlanCategoryRepository;
    
    /** @var RepositoryInterface */
    protected $pricingPlansRepository;
    
    /** @var RepositoryInterface */
    protected $paymentMethodsRepository;
    
    /** @var RepositoryInterface */
    protected $subscriptionsRepository;
    
    /** @var Payment */
    protected $vsPayment;
    
    /** @vvar OrderFactory */
    protected $orderFactory;
    
    /** @var RepositoryInterface */
    protected $gatewaysRepository;
    
    public function __construct(
        ManagerRegistry $doctrine,
        EventDispatcherInterface $eventDispatcher,
        SecurityBridge $securityBridge,
        Factory $ordersFactory,
        RepositoryInterface $ordersRepository,
        Factory $orderItemsFactory,
        RepositoryInterface $pricingPlanCategoryRepository,
        RepositoryInterface $pricingPlansRepository,
        RepositoryInterface $paymentMethodsRepository,
        RepositoryInterface $subscriptionsRepository,
        Payment $vsPayment,
        OrderFactory $orderFactory,
        RepositoryInterface $gatewaysRepository
    ) {
        $this->doctrine                         = $doctrine;
        $this->eventDispatcher                  = $eventDispatcher;
        $this->securityBridge                   = $securityBridge;
        $this->ordersFactory                    = $ordersFactory;
        $this->ordersRepository                 = $ordersRepository;
        $this->orderItemsFactory                = $orderItemsFactory;
        $this->pricingPlanCategoryRepository    = $pricingPlanCategoryRepository;
        $this->pricingPlansRepository           = $pricingPlansRepository;
        $this->paymentMethodsRepository         = $paymentMethodsRepository;
        $this->subscriptionsRepository          = $subscriptionsRepository;
        $this->vsPayment                        = $vsPayment;
        $this->orderFactory                     = $orderFactory;
        $this->gatewaysRepository               = $gatewaysRepository;
    }
    
    public function showPricingPlans( Request $request ): Response
    {
        $pricingPlanCategories  = $this->pricingPlanCategoryRepository->findAll();
        
        return $this->render( '@VSPayment/Pages/PricingPlansCheckout/pricing_plans.html.twig', [
            'pricingPlanCategories' => $pricingPlanCategories,
        ]);
    }
    
    public function showSelectPricingPlanForm( $pricingPlanId, Request $request ): Response
    {
        $form                   = $this->createForm( SelectPricingPlanForm::class, null, ['method' => 'POST'] );
        $bankTransferGateway    = $this->gatewaysRepository->findOneBy( ['factoryName' => 'offline_bank_transfer'] );
        
        return $this->render( '@VSPayment/Pages/PricingPlansCheckout/Partial/select-pricing-plan-form.html.twig', [
            'form'              => $form->createView(),
            'pricingPlanId'     => $pricingPlanId,
            'bankTransferInfo'  => $bankTransferGateway ? $bankTransferGateway->getConfig() : null,
        ]);
    }
    
    public function handlePricingPlanFormAction( Request $request ): Response
    {
        $cart   = $this->orderFactory->getShoppingCart();
        if ( ! $cart ) {
            throw new ShoppingCartException( 'Shopping Cart cannot be created !!!' );
        }
        
        $form   = $this->createForm( SelectPricingPlanForm::class );
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em             = $this->doctrine->getManager();
            $formData       = $form->getData();
            
            $paymentMethod  = $this->paymentMethodsRepository->find( $formData['paymentMethod'] );
            $pricingPlan    = $this->prepareCart( $formData, $cart, $paymentMethod );
            
            $paymentPrepareUrl  = $this->vsPayment->getPaymentPrepareRoute(
                $paymentMethod->getGateway(),
                //$pricingPlan->isRecurringPayment()
                false
            );
            
            return new JsonResponse([
                'status'    => Status::STATUS_OK,
                'data'      => [
                    'paymentPrepareUrl' => $paymentPrepareUrl,
                    'gatewayFactory'    => $paymentMethod->getGateway()->getFactoryName(),
                ]
            ]);
        }
    }
    
    protected function prepareCart( $formData, $cart, $paymentMethod )
    {
        $em             = $this->doctrine->getManager();
        $pricingPlan    = $this->pricingPlansRepository->find( $formData['pricingPlan'] );
        $subscription   = $this->getSubscription( $pricingPlan );
        if ( ! $subscription ) {
            throw new CheckoutException( 'Subscription Cannot be Created !' );
        }
        
        $orderItem      = $this->orderItemsFactory->createNew();
        
        $orderItem->setPaidServiceSubscription( $subscription );
        $orderItem->setPrice( $pricingPlan->getPrice() );
        $orderItem->setCurrencyCode( $pricingPlan->getCurrencyCode() );
        
        $cart->addItem( $orderItem );
        
        //$cart->setRecurringPayment( $pricingPlan->isRecurringPayment() );
        $cart->setPaymentMethod( $paymentMethod );
        $cart->setDescription( $pricingPlan->getDescription() );
        
        $em->persist( $cart );
        $em->flush();
        
        return $pricingPlan;
    }
    
    protected function getSubscription( $pricingPlan )
    {
        $user               = $this->securityBridge->getUser();
        $userSubscriptions  = $user->getPricingPlanSubscriptions();
        
        if ( $userSubscriptions->containsKey( $pricingPlan->getSubscriptionCode() ) ) {
            $subscription   = $userSubscriptions->get( $pricingPlan->getSubscriptionCode() );
        } else {
            $this->eventDispatcher->dispatch(
                new CreateSubscriptionEvent( $pricingPlan ),
                CreateSubscriptionEvent::NAME
            );
            
            $this->doctrine->getManager()->refresh( $user );
            $subscription   = $user->getPricingPlanSubscriptions()->get( $pricingPlan->getSubscriptionCode() );
        }
        
        return $subscription;
    }
}