<?php namespace Vankosoft\PaymentBundle\Templating\Helper;

interface CurrencyHelperInterface
{
    public function convertCurrencyCodeToSymbol( string $code ): string;
}
