<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\Persistence\ManagerRegistry;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Vankosoft\PaymentBundle\Component\Payment\Payment;

/**
 * Some Custom Actions on Received Payments
 * 
 * @author i.atanasov77@gmail.com
 */
class PaymentController extends AbstractController
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var RepositoryInterface */
    private $paymentRepository;
    
    /** @var Payment */
    private $vsPayment;
    
    public function __construct(
        ManagerRegistry $doctrine,
        RepositoryInterface $paymentRepository,
        Payment $vsPayment
    ) {
        $this->doctrine             = $doctrine;
        $this->paymentRepository    = $paymentRepository;
        $this->vsPayment            = $vsPayment;
    }
    
    public function setPaymentPaidAction( $paymentId, Request $request ): Response
    {
        $payment    = $this->paymentRepository->find( $paymentId );
        
        switch ( $payment->getFactoryName() ) {
            case 'offline_bank_transfer':
                $this->vsPayment->setBankTransferPaymentPaid( $payment );
                break;
            default:
                throw new \Exception( 'NOT Allowed to Change Payments on this Payment Gateway !!!' );
        }
        
        return $this->redirectToRoute( 'vs_payment_payment_index' );
    }
}