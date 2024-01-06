<?php namespace Vankosoft\PaymentBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Vankosoft\PaymentBundle\Component\Payment\Payment;
use Vankosoft\PaymentBundle\Model\Interfaces\GatewayConfigInterface;
use Vankosoft\PaymentBundle\Model\Interfaces\PaymentInterface;

final class PaymentExtension extends AbstractExtension
{
    /** @var Payment */
    private $vsPayment;
    
    public function __construct( Payment $vsPayment )
    {
        $this->vsPayment    = $vsPayment;
    }
    
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'supportRecurring', [$this, 'isSupportRecurring'] ),
            new TwigFilter( 'paid', [$this, 'isPaid'] ),
        ];
    }
    
    public function isSupportRecurring( GatewayConfigInterface $gateway ): bool
    {
        return $this->vsPayment->isGatewaySupportRecurring( $gateway );
    }
    
    public function isPaid( PaymentInterface $payment ): bool
    {
        return $this->vsPayment->isPaymentPaid( $payment );
    }
}
