<?php namespace Vankosoft\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Payum\Bundle\PayumBundle\Controller\PayumController;

use Vankosoft\PaymentBundle\Form\PaymentMethod as PaymentMethodForm;
use Vankosoft\PaymentBundle\Entity\PaymentMethod;

class PaymentMethodController extends PayumController
{
    
    public function indexAction( Request $request )
    {
        $er     = $this->getDoctrine()->getRepository( PaymentMethod::class );
        
        return $this->render( 'IAPaymentBundle:PaymentMethodConfig:index.html.twig', [
            'methods' => $er->findAll()
        ]);
    }
    
    /**
     * Prepare Action
     * 
     * @return type
     */
    public function configAction( $id, Request $request )
    {
        $er     = $this->getDoctrine()->getRepository( PaymentMethod::class );
        $paymentMethod = $id ? $er->find( $id ) : new PaymentMethod();
        
        $form = $this->createForm( PaymentMethodForm::class, $paymentMethod );
     
        // Form Submit
        $form->handleRequest( $request );
        if ( $form->isSubmitted() ) {
            $em = $this->getDoctrine()->getManager();
            $em->persist( $form->getData() );
            $em->flush();
            
           return $this->redirect( $this->generateUrl( 'ia_payment_methods_index' ) );
        }
        
        return $this->render( 'IAPaymentBundle:PaymentMethodConfig:config.html.twig', [
            'form'      => $form->createView()
        ]);
    }
    
}
