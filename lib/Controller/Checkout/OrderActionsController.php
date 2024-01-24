<?php namespace Vankosoft\PaymentBundle\Controller\Checkout;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Vankosoft\ApplicationBundle\Component\Status;
use Vankosoft\PaymentBundle\Model\Order;
use Vankosoft\PaymentBundle\Component\Catalog\PricingPlanSubscriptionsBridge;

class OrderActionsController extends AbstractController
{
    /** @var TranslatorInterface */
    protected $translator;
    
    /** @var ManagerRegistry */
    protected $doctrine;
    
    /** @var RepositoryInterface */
    protected $ordersRepository;
    
    /** @var PricingPlanSubscriptionsBridge */
    protected $subscriptionsBridge;
    
    public function __construct(
        TranslatorInterface $translator,
        ManagerRegistry $doctrine,
        RepositoryInterface $orderRepository,
        PricingPlanSubscriptionsBridge $subscriptionsBridge
    ) {
        $this->translator           = $translator;
        $this->doctrine             = $doctrine;
        $this->ordersRepository     = $orderRepository;
        $this->subscriptionsBridge  = $subscriptionsBridge;
    }
    
    public function setOrderStatusPaid( $orderId, Request $request ): Response
    {
        $order  = $this->ordersRepository->find( $orderId );
        
        $order->setStatus( Order::STATUS_PAID_ORDER );
        $this->doctrine->getManager()->persist( $order );
        $this->doctrine->getManager()->flush();
        
        $this->subscriptionsBridge->triggerSubscriptionsPaymentDone( $order->getSubscriptions() );
        
        $flashMessage   = $this->translator->trans( 'vs_payment.template.pricing_plan_payment_success', [], 'VSPaymentBundle' );
        $request->getSession()->getFlashBag()->add( 'notice', $flashMessage );
        
        return new JsonResponse([
            'status'    => Status::STATUS_OK,
        ]);
    }
}