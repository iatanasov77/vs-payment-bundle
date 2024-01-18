<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Review\Model\ReviewableInterface;

interface ContentServiceInterface extends ResourceInterface, TranslatableInterface, ReviewableInterface
{
    
}