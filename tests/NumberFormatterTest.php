<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Twig\Extensions\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTest extends TestCase
{
    private NumberFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter();
    }

    public function testFormatInt(): void
    {
        // Testování pro celé číslo
        $result = $this->formatter->formatPrice(1234567);
        $this->assertEquals('1 234 567,00', $result);
    }

    public function testFormatFloat(): void
    {
        // Testování pro desetinné číslo
        $result = $this->formatter->formatPrice(1234567.89);
        $this->assertEquals('1 234 567,89', $result);
    }

    public function testFormatFloatWithDecimals(): void
    {
        // Testování pro desetinné číslo s více než 2 desetinnými místy
        $result = $this->formatter->formatPrice(1234567.895, null, 3);
        $this->assertEquals('1 234 567,895', $result);
    }

    public function testFormatString(): void
    {
        // Testování pro řetězec
        $result = $this->formatter->formatPrice('Hello World');
        $this->assertEquals('Hello World', $result);
    }

    public function testFormatCurrencyPrefix(): void
    {
        // Testování pro měnu s prefixem
        $result = $this->formatter->formatPrice(1234567.89, SupportedCurrencies::EUR);
        $this->assertEquals('€ 1 234 567,89', $result);
    }

    public function testFormatCurrencyPostfix(): void
    {
        // Testování pro měnu s postfixem
        $result = $this->formatter->formatPrice(1234567.89, SupportedCurrencies::CZK);
        $this->assertEquals('1 234 567,89 Kč', $result);
    }

    public function testFormatUnsupportedCurrency(): void
    {
        // Testování pro neznámou měnu
        $result = $this->formatter->formatPrice(1234567.89, 'ABC');
        $this->assertEquals('1 234 567,89 ABC', $result);
    }
}