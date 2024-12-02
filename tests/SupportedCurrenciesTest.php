<?php

namespace Lemonade\EmailGenerator\Tests\Localization;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use PHPUnit\Framework\TestCase;

class SupportedCurrenciesTest extends TestCase
{

    /**
     * Testuje, zda metoda isPrefix() správně určuje, jestli má být symbol měny prefix nebo suffix.
     */
    public function testIsPrefix(): void
    {
        $prefixCurrencies = [
            SupportedCurrencies::EUR,
            SupportedCurrencies::USD,
            SupportedCurrencies::GBP,
            SupportedCurrencies::CHF,
            SupportedCurrencies::JPY,
            SupportedCurrencies::KRW,
            SupportedCurrencies::BRL,
            SupportedCurrencies::SGD,
            SupportedCurrencies::HKD,
            SupportedCurrencies::NZD,
        ];

        foreach (SupportedCurrencies::cases() as $currency) {
            if (in_array($currency, $prefixCurrencies, true)) {
                $this->assertTrue($currency->isPrefix(), "Měna {$currency->name} by měla být prefix");
            } else {
                $this->assertFalse($currency->isPrefix(), "Měna {$currency->name} by neměla být prefix");
            }
        }
    }
}
