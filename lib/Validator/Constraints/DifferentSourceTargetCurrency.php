<?php namespace Vankosoft\PaymentBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

class DifferentSourceTargetCurrency extends Constraint
{
    /** @var string */
    public $message = 'vs_payment.validation.different_source_target_currency';
    
    #[HasNamedArguments]
    public function __construct( ?array $options = null, ?string $message = null, ?array $groups = null, mixed $payload = null )
    {
        if ( \is_array( $options ) ) {
            trigger_deprecation( 'symfony/validator', '7.3', 'Passing an array of options to configure the "%s" constraint is deprecated, use named arguments instead.', static::class );
        }
        
        parent::__construct( $options, $groups, $payload );
        
        $this->message = $message ?? $this->message;
    }
    
    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}