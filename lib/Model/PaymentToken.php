<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Token;
use Sylius\Component\Resource\Model\ResourceInterface;

class PaymentToken extends Token implements ResourceInterface
{
    /** @var int */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}
