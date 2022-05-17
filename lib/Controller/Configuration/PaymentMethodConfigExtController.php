<?php namespace Vankosoft\PaymentBundle\Controller\Configuration;

use Symfony\Component\HttpFoundation\Request;
use Payum\Bundle\PayumBundle\Controller\PayumController;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Resource\Factory\Factory;

use Vankosoft\PaymentBundle\Form\PaymentMethodForm;

class PaymentMethodConfigExtController extends PayumController
{
    /** @var string */
    protected $paymentMethodClass;
    
    /** @var EntityRepository */
    protected EntityRepository $paymentMethodRepository;
    
    /** @var Factory */
    protected Factory $paymentMethodFactory;
    
    public function __construct(
        string $paymentMethodClass,
        EntityRepository $paymentMethodRepository,
        Factory $paymentMethodFactory
    ) {
        $this->paymentMethodClass       = $paymentMethodClass;
        $this->paymentMethodRepository  = $paymentMethodRepository;
        $this->paymentMethodFactory     = $paymentMethodFactory;
    }
    
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
            $em = $this->getDoctrine()->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
           return $this->redirect( $this->generateUrl( 'vs_payment_methods_index' ) );
        }
        
        return $this->render( '@VSPayment/Pages/PaymentMethodConfigExt/config.html.twig', [
            'form'      => $form->createView()
        ]);
    }
}
