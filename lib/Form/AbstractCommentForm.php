<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

abstract class AbstractCommentForm extends AbstractResourceType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add( 'video', HiddenType::class, ['mapped' => false, 'data' => $options['video']] )
            ->add( 'parent', HiddenType::class, ['mapped' => false, 'data' => $options['parent_comment']] )
        
            ->add( 'comment', TextareaType::class, [
                'label' => 'vs_payment.form.comment.comment',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.comment.comment_placeholder'
                ],
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label' => 'vs_payment.form.comment.submit',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'translation_domain'    => 'VSPaymentBundle',
            
            'video'                 => 0,
            'parent_comment'        => 0,
        ]);
    }
}