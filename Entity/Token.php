<?php
namespace IA\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token as BaseToken;

/**
 * @ORM\Table(name="IAP_Tokens")
 * @ORM\Entity
 */
class Token extends BaseToken
{
}
