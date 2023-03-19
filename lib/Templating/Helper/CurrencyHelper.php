<?php namespace Vankosoft\PaymentBundle\Templating\Helper;

use Symfony\Component\Intl\Currencies;
use Symfony\Component\Templating\Helper\Helper;

class CurrencyHelper extends Helper implements CurrencyHelperInterface
{
    public function convertCurrencyCodeToSymbol( string $code ): string
    {
        return Currencies::getSymbol( $code );
    }
    
    public function getName(): string
    {
        return 'vs_payment_currency';
    }
}
