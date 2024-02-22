<?php namespace Vankosoft\PaymentBundle\Component\Promotion\RuleChecker;

use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

use Vankosoft\PaymentBundle\Model\Interfaces\UserPaymentAwareInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\OrderInterface;
use Vankosoft\PaymentBundle\Repository\OrderRepository;

final class NthOrderRuleChecker implements RuleCheckerInterface
{
    public const TYPE = 'nth_order';
    
    /** @var OrderRepository */
    private $orderRepository;
    
    public function __construct( OrderRepository $orderRepository )
    {
        $this->orderRepository  = $orderRepository;
    }
    
    /**
     * @throws UnsupportedTypeException
     */
    public function isEligible( PromotionSubjectInterface $subject, array $configuration ): bool
    {
        if ( ! $subject instanceof OrderInterface ) {
            throw new UnsupportedTypeException( $subject, OrderInterface::class );
        }
        
        if ( ! isset( $configuration['nth'] ) || ! is_int( $configuration['nth'] ) ) {
            return false;
        }
        
        $customer = $subject->getCustomer();
        if ( ! $customer instanceof UserPaymentAwareInterface ) {
            return false;
        }
        
        //eligible if it is first order of guest and the promotion is on first order
        if ( null === $customer->getId() ) {
            return 1 === $configuration['nth'];
        }
        
        return $this->orderRepository->countByCustomer( $customer ) === ( $configuration['nth'] - 1 );
    }
}
