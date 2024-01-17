<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractReviewForm extends AbstractResourceType
{
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices' => $this->createRatingList( $options['rating_steps'] ),
                'label' => 'sylius.form.review.rating',
                'expanded' => $options['rating_expanded'] ,
                'multiple' => false,
            ])
            
            ->add('title', TextType::class, [
                'label' => 'sylius.form.review.title',
            ])
            
            ->add('comment', TextareaType::class, [
                'label' => 'sylius.form.review.comment',
            ])
        ;
    }
    
    public function configureOptions( OptionsResolver $resolver ): void
    {
        parent::configureOptions( $resolver );
        
        $resolver->setDefaults([
            'rating_steps'      => 5,
            'rating_expanded'   => true,
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