<?php namespace Vankosoft\PaymentBundle\Component\Promotion;

use Symfony\Component\Form\FormInterface;
use Sylius\Component\Promotion\Generator\ReadablePromotionCouponGeneratorInstructionInterface;

final class PromotionCouponGeneratorInstruction implements ReadablePromotionCouponGeneratorInstructionInterface
{
    /** @var FormInterface */
    private $form;
    
    public function __construct( FormInterface $form )
    {
        $this->form = $form;
    }
    
    public function getAmount(): ?int
    {
        return isset( $this->form['amount'] ) ? $this->form['amount']->getData() : null;
    }
    
    public function getPrefix(): ?string
    {
        return isset( $this->form['prefix'] ) ? $this->form['prefix']->getData() : null;
    }
    
    public function getCodeLength(): ?int
    {
        return isset( $this->form['codeLength'] ) ? $this->form['codeLength']->getData() : null;
    }
    
    public function getSuffix(): ?string
    {
        return isset( $this->form['suffix'] ) ? $this->form['suffix']->getData() : null;
    }
    
    public function getExpiresAt(): ?\DateTimeInterface
    {
        return isset( $this->form['expiresAt'] ) ? $this->form['expiresAt']->getData() : null;
    }
    
    public function getUsageLimit(): ?int
    {
        return isset( $this->form['usageLimit'] ) ? $this->form['usageLimit']->getData() : null;
    }
}