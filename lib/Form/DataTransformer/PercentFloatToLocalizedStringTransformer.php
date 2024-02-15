<?php namespace Vankosoft\PaymentBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\PercentToLocalizedStringTransformer;

final class PercentFloatToLocalizedStringTransformer extends PercentToLocalizedStringTransformer
{
    /**
     * Transforms between a percentage value into a float
     *
     * @param string $value Percentage value
     *
     * @return float Normalized value
     *
     * @throws TransformationFailedException if the given value is not a string or
     *                                       if the value could not be transformed
     */
    public function reverseTransform( $value )
    {
        if ( '' === $value ) {
            return;
        }
        
        return (float) parent::reverseTransform( $value );
    }
}