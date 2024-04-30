<?php namespace Vankosoft\PaymentBundle\Component\Promotion;

use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

final class PromotionCouponGeneratorInstruction implements ReadablePromotionCouponGeneratorInstructionInterface
{
    /** @var array */
    private $params;
    
    public function __construct( array $params )
    {
        $this->params   = $params;
    }
    
    public function getAmount(): ?int
    {
        return isset( $this->params['amount'] ) ? $this->params['amount'] : null;
    }
    
    public function getPrefix(): ?string
    {
        return isset( $this->params['prefix'] ) ? $this->params['prefix'] : null;
    }
    
    public function getCodeLength(): ?int
    {
        return isset( $this->params['codeLength'] ) ? $this->params['codeLength'] : null;
    }
    
    public function getSuffix(): ?string
    {
        return isset( $this->params['suffix'] ) ? $this->params['suffix'] : null;
    }
    
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return isset( $this->params['expiresAt'] ) ? $this->params['expiresAt'] : null;
    }
    
    public function getUsageLimit(): ?int
    {
        return isset( $this->params['usageLimit'] ) ? $this->params['usageLimit'] : null;
    }
}