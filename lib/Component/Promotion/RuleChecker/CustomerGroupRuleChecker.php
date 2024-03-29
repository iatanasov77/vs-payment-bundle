<?php namespace Vankosoft\PaymentBundle\Component\Promotion\RuleChecker;

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;

class CustomerGroupRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'customer_group';
    
    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible( PromotionSubjectInterface $subject, array $configuration ): bool
    {
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnsupportedTypeException( $subject, OrderInterface::class );
        }
        
        if ( null === $customer = $subject->getCustomer() ) {
            return false;
        }
        
        if ( ! $customer instanceof UserPaymentAwareInterface ) {
            return false;
        }
        
        if ( null === $customer->getGroup() ) {
            return false;
        }
        
        return $configuration['group_code'] === $customer->getGroup()->getCode();
    }
}
