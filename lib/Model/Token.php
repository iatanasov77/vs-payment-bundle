<?php namespace Vankosoft\PaymentBundle\Model;

use Payum\Core\Model\Token as BaseToken;
use Sylius\Component\Resource\Model\ResourceInterface;

class Token extends BaseToken implements ResourceInterface
{
    /**
     * @var int
     */
    protected $id;
    
    public function getId()
    {
        return $this->id;
    }
}
