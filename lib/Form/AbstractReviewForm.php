<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

abstract class AbstractReviewForm extends AbstractResourceType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices'       => $this->createRatingList( $options['rating_steps'] ),
                'label'         => 'vs_payment.form.review.rating',
                'placeholder'   => 'vs_payment.form.review.rating_placeholder',
                'expanded'      => $options['rating_expanded'] ,
                'multiple'      => false,
            ])
            
            ->add('title', TextType::class, [
                'label' => 'vs_payment.form.review.title',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.review.title_placeholder'
                ],
            ])
            
            ->add('comment', TextareaType::class, [
                'label' => 'vs_payment.form.review.comment',
                'attr'  => [
                    'placeholder' => 'vs_payment.form.review.comment_placeholder'
                ],
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label' => 'vs_payment.form.review.submit',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'translation_domain'    => 'VSPaymentBundle',
            
            'rating_steps'          => 5,
            'rating_expanded'       => true,
        ]);
    }
    
    private function createRatingList( int $maxRate ): array
    {
        $ratings = [];
        for ( $i = 1; $i <= $maxRate; ++$i ) {
            $ratings[$i] = $i;
        }
        
        return $ratings;
    }
}