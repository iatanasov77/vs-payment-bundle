<?php namespace Vankosoft\PaymentBundle\Model\Interfaces;

interface ReviewerAwareInterface
{
    public function _toReviewer(): ReviewerInterface;
}