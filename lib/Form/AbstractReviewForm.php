<?php namespace Vankosoft\PaymentBundle\Form;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Vankosoft\PaymentBundle\Model\Interfaces\ReviewInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

abstract class AbstractReviewForm extends AbstractResourceType
{
    /** @var TranslatorInterface */
    protected $translator;
    
    public function __construct(
        string $dataClass,
        TranslatorInterface $translator
    ) {
        parent::__construct( $dataClass );
        
        $this->translator   = $translator;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options ): void
    {
        $builder
            ->add('rating', ChoiceType::class, [
                'choices'       => \array_flip( $this->createRatingList( $options['rating_steps'] ) ),
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
            
//         $builder->addEventListener( FormEvents::PRE_SET_DATA, function ( FormEvent $event ): void {
//             $review = $event->getData();
            
//             Assert::isInstanceOf( $review, ReviewInterface::class );
//         });
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
            $label  = ( $i > 1 ) ?
                        $this->translator->trans( 'vs_payment.form.review.rating_stars', [], 'VSPaymentBundle' ) :
                        $this->translator->trans( 'vs_payment.form.review.rating_star', [], 'VSPaymentBundle' );
            
            $ratings[$i] = \sprintf( "%d %s", $i, $label );
        }
        
        return $ratings;
    }
}