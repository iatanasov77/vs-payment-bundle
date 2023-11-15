<?php namespace Vankosoft\PaymentBundle\Controller\PricingPlans;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Factory\Factory;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\UsersBundle\Security\SecurityBridge;
use Vankosoft\PaymentBundle\Component\OrderFactory;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Component\Exception\ShoppingCartException;
use Vankosoft\PaymentBundle\Model\Interfaces\PayableObjectInterface;
use Vankosoft\PaymentBundle\Form\SelectPricingPlanForm;

class PricingPlanCheckoutController extends AbstractController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
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
    
    public function __construct(
        ManagerRegistry $doctrine,
        SecurityBridge $securityBridge,
        Factory $ordersFactory,
        RepositoryInterface $ordersRepository,
        Factory $orderItemsFactory,
        RepositoryInterface $pricingPlanCategoryRepository,
        RepositoryInterface $pricingPlansRepository,
        RepositoryInterface $paymentMethodsRepository,
        RepositoryInterface $subscriptionsRepository,
        Payment $vsPayment,
        OrderFactory $orderFactory
    ) {
        $this->doctrine                         = $doctrine;
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
    }
    
    public function showPricingPlans( Request $request ): Response
    {
        $pricingPlanCategories  = $this->pricingPlanCategoryRepository->findAll();
        
        return $this->render( '@VSPayment/Pages/PricingPlansCheckout/pricing_plans.html.twig', [
            'pricingPlanCategories' => $pricingPlanCategories,
        ]);
    }
    
    public function showSelectPricingPlanForm( $pricingPlanId, $subscriptionId, Request $request ): Response
    {
        $form   = $this->createForm( SelectPricingPlanForm::class, null, ['method' => 'POST'] );
        
        return $this->render( '@VSPayment/Pages/PricingPlansCheckout/Partial/select-pricing-plan-form.html.twig', [
            'form'              => $form->createView(),
            'pricingPlanId'     => $pricingPlanId,
            'subscriptionId'    => $subscriptionId,
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
            $pricingPlan    = $this->addPricingPlanToCart( $formData['pricingPlan'], $cart );
            $paymentMethod  = $this->paymentMethodsRepository->find( $formData['paymentMethod'] );
            
            $subscriptionId = intval( $formData['subscription'] );
            if ( $subscriptionId ) {
                $subscription   = $this->subscriptionsRepository->find( $subscriptionId );
                $cart->setSubscription( $subscription );
            }
            
            $cart->setRecurringPayment( $pricingPlan->isRecurringPayment() );
            $cart->setPaymentMethod( $paymentMethod );
            $cart->setDescription( $pricingPlan->getDescription() );
            $em->persist( $cart );
            $em->flush();
            
            $paymentPrepareUrl  = $this->vsPayment->getPaymentPrepareRoute(
                $paymentMethod->getGateway(),
                $pricingPlan->isRecurringPayment()
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
    
    protected function addPricingPlanToCart( $pricingPlanId, &$cart ): PayableObjectInterface
    {
        $em             = $this->doctrine->getManager();
        
        $orderItem      = $this->orderItemsFactory->createNew();
        $payableObject  = $this->pricingPlansRepository->find( $pricingPlanId );
        
        $orderItem->setPaidServiceSubscription( $payableObject );
        $orderItem->setPrice( $payableObject->getPrice() );
        $orderItem->setCurrencyCode( $payableObject->getCurrencyCode() );
        
        $cart->addItem( $orderItem );
        $em->persist( $cart );
        $em->flush();
        
        return $payableObject;
    }
}