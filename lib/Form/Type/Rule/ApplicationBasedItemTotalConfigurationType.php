<?php namespace Vankosoft\PaymentBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Vankosoft\ApplicationBundle\Model\Interfaces\ApplicationInterface;
use Vankosoft\ApplicationBundle\Form\Type\ApplicationCollectionType;

final class ApplicationBasedItemTotalConfigurationType extends AbstractType
{
    public function configureOptions( OptionsResolver $resolver ): void
    {
        $resolver->setDefaults([
            'entry_type'    => ItemTotalConfigurationType::class,
            'entry_options' => fn ( ApplicationInterface $application ) => [
                'label'     => $application->getTitle(),
                
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
