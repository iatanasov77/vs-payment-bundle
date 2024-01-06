<?php namespace Vankosoft\PaymentBundle\CustomGateways\Keys;

class OfflineBankTransferKeys
{
    /** @var string */
    protected $iban;
    
    /** @var string */
    protected $bankName;
    
    /** @var string */
    protected $recieverName;
    
    /** @var string */
    protected $reason;
    
    public function __construct( string $iban, string $bankName, string $recieverName, string $reason )
    {
        $this->iban         = $iban;
        $this->bankName     = $bankName;
        $this->recieverName = $recieverName;
        $this->reason       = $reason;
    }
    
    /**
     * @return string
     */
    public function getIban()
    {
        return $this->iban;
    }
    
    /**
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }
    
    /**
     * @return string
     */
    public function getRecieverName()
    {
        return $this->recieverName;
    }
    
    /**
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }
}