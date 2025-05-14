<?php namespace Vankosoft\PaymentBundle\Validator\Constraints;

use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

class DifferentSourceTargetCurrencyValidator extends ConstraintValidator
{
    public function validate( $value, Constraint $constraint ): void
    {
        /** @var DifferentSourceTargetCurrency $constraint */
        Assert::isInstanceOf( $constraint, DifferentSourceTargetCurrency::class );
        
        if ( ! $value instanceof ExchangeRateInterface ) {
            throw new UnexpectedTypeException( $value, ExchangeRateInterface::class );
        }
        
        if ( $value->getSourceCurrency() === $value->getTargetCurrency() ) {
            $this->context->buildViolation( $constraint->message )->addViolation();
        }
    }
}