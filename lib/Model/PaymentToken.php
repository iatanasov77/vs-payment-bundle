<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Token;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableTrait;

class PaymentToken extends Token implements ResourceInterface
{
    use TimestampableTrait;
    
    /** @var int */
    protected $id;
    
    public function getId()
    {
        //return $this->id;
        return $this->getHash();
    }
}
