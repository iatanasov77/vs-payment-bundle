<?php namespace Vankosoft\PaymentBundle\Form\Type\Action;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Form\Type\ApplicationCollectionType;
use Vankosoft\PaymentBundle\Form\Type\Action\UnitFixedDiscountConfigurationType;

final class ApplicationBasedUnitFixedDiscountConfigurationType extends AbstractType
{
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'entry_type' => UnitFixedDiscountConfigurationType::class,
            'entry_options' => fn ( ApplicationInterface $application ) => [
                'label' => $application->getTitle(),
                
                // I need to add Application Base Currency in Application Settings OR in Service Parameters
                // ========================================================================================
                //'currency'  => $application->getBaseCurrency()->getCode(),
            ],
        ]);
    }

    public function getParent(): string
    {
        return ApplicationCollectionType::class;
    }
}
