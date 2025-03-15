<?php namespace Vankosoft\PaymentBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\Intl\Currencies;

final class CurrencyExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'vs_currency_symbol', [$this, 'convertCurrencyCodeToSymbol'] ),
            new TwigFilter( 'vs_currency_name', [$this, 'convertCurrencyCodeToName'] ),
        ];
    }
    
    public function convertCurrencyCodeToSymbol( string $code ): string
    {
        return Currencies::getSymbol( $code );
    }
    
    public function convertCurrencyCodeToName( string $code ): string
    {
        return Currencies::getName( $code );
    }
}
