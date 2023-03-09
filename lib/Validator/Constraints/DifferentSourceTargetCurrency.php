<?php namespace Vankosoft\PaymentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class DifferentSourceTargetCurrency extends Constraint
{
    /** @var string */
    public $message = 'vs_payment.validation.different_source_target_currency';
    
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}