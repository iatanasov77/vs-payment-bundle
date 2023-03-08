<?php namespace Vankosoft\PaymentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class UniqueCurrencyPair extends Constraint
{
    /** @var string */
    public $message = 'vs_payment.validation.unique_currency_pair';
    
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}