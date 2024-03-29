<?php namespace Vankosoft\PaymentBundle\Twig;

use Vankosoft\PaymentBundle\Templating\Helper\CurrencyHelperInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class CurrencyExtension extends AbstractExtension
{
    /** @var CurrencyHelperInterface */
    private $helper;
    
    public function __construct( CurrencyHelperInterface $helper )
    {
        $this->helper   = $helper;
    }
    
    public function getFilters(): array
    {
        return [
            new TwigFilter( 'vs_currency_symbol', [$this->helper, 'convertCurrencyCodeToSymbol'] ),
            new TwigFilter( 'vs_currency_name', [$this->helper, 'convertCurrencyCodeToName'] ),
        ];
    }
}
