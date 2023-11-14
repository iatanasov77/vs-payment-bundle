<?php namespace Vankosoft\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Vankosoft\PaymentBundle\Model\Interfaces\PricingPlanInterface;

class SelectPricingPlanForm extends AbstractType
{
    /** @var string */
    private $pricingPlanClass;
    
    /** @var string */
    private $paymentMethodClass;
    
    /** @var RepositoryInterface */
    private $pricingPlanRepository;
    
    public function __construct(
        string $pricingPlanClass,
        string $paymentMethodClass,
        RepositoryInterface $pricingPlanRepository
    ) {
        $this->pricingPlanClass         = $pricingPlanClass;
        $this->paymentMethodClass       = $paymentMethodClass;
        $this->pricingPlanRepository    = $pricingPlanRepository;
    }
    
    public function buildForm( FormBuilderInterface $builder, array $options )
    {
        $builder
            ->add( 'subscription', HiddenType::class, ['data' => '0'])
        
            ->add( 'name', TextType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.name',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'                  => ['placeholder' => 'vs_vvp.form.select_pricing_plan.name_placeholder'],
            ])
            
            ->add( 'email', EmailType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.email',
                'translation_domain'    => 'VSPaymentBundle',
                'attr'                  => ['placeholder' => 'example@domain.com'],
            ])
            
            ->add( 'pricingPlan', ChoiceType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.choose_plan',
                'translation_domain'    => 'VSPaymentBundle',
                'choices'               => $this->pricingPlanRepository->findAllForForm(),
            ])
            
            ->add( 'paymentMethod', EntityType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.payment_method',
                'translation_domain'    => 'VSPaymentBundle',
                'class'                 => $this->paymentMethodClass,
                'choice_label'          => 'name',
                'expanded'              => true,
            ])
            
            ->add( 'btnSubmit', SubmitType::class, [
                'label'                 => 'vs_payment.form.select_pricing_plan.submit',
                'translation_domain'    => 'VSPaymentBundle',
            ])
        ;
    }
}
