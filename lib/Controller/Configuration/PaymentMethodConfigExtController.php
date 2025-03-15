<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Symfony\Component\HttpFoundation\Request;
use Payum\Bundle\PayumBundle\Controller\PayumController;
use Doctrine\Persistence\ManagerRegistry;

use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Form\PaymentMethodForm;

class PaymentMethodConfigExtController extends PayumController
{
    /** @var ManagerRegistry */
    protected ManagerRegistry $doctrine;
    
    /** @var string */
    protected $paymentMethodClass;
    
    /** @var RepositoryInterface */
    protected RepositoryInterface $paymentMethodRepository;
    
    /** @var Factory */
    protected Factory $paymentMethodFactory;
    
    public function __construct(
        ManagerRegistry $doctrine,
        string $paymentMethodClass,
        RepositoryInterface $paymentMethodRepository,
        Factory $paymentMethodFactory
    ) {
        $this->doctrine                 = $doctrine;
        $this->paymentMethodClass       = $paymentMethodClass;
        $this->paymentMethodRepository  = $paymentMethodRepository;
        $this->paymentMethodFactory     = $paymentMethodFactory;
    }
    
    /** @NOTE This Action Not Used Anymore */
    public function indexAction( Request $request )
    {
        return $this->render( '@VSPayment/Pages/PaymentMethodConfigExt/index.html.twig', [
            'methods' => $this->paymentMethodRepository->findAll()
        ]);
    }
    
    /**
     * Prepare Action
     * 
     * @return type
     */
    public function configAction( $id, Request $request )
    {
        if ( $id ) {
            $paymentMethod = $this->paymentMethodRepository->find( $id );
        } else {
            $paymentMethod = $this->paymentMethodFactory->createNew();
        }
        
        $form = $this->createForm( PaymentMethodForm::class, $paymentMethod );
     
        // Form Submit
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em = $this->doctrine->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
           return $this->redirect( $this->generateUrl( 'vs_payment_payment_method_index' ) );
        }
        
        return $this->render( '@VSPayment/Pages/PaymentMethodConfigExt/config.html.twig', [
            'form'      => $form->createView(),
            'item'      => $paymentMethod,
        ]);
    }
}
