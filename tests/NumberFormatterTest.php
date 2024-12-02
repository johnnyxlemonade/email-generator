<?php

namespace Lemonade\EmailGenerator\Tests\Twig\Extensions;

use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Twig\Extensions\NumberFormatter;
use PHPUnit\Framework\TestCase;

class NumberFormatterTest extends TestCase
{
    private NumberFormatter $formatter;

    /**
     * Inicializace `NumberFormatter` před každým testem.
     */
    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter();
    }

    /**
     * Testuje formátování číselné hodnoty s platným měnovým suffixem.
     * Ověřuje, že hodnota `12345.67` s měnou `EUR` bude formátována jako `€ 12 345,67`.
     */
    public function testFormatWithValidCurrencySuffix(): void
    {
        $result = $this->formatter->format(12345.67, 'EUR');
        $this->assertSame('€ 12 345,67', $result);
    }

    /**
     * Testuje formátování číselné hodnoty s neplatným měnovým suffixem.
     * Pokud není suffix znám (např. `XYZ`), výsledek bude původní hodnota s připojeným suffixem.
     * Ověřuje, že hodnota `12345.67` s měnou `XYZ` bude `12 345,67 XYZ`.
     */
    public function testFormatWithInvalidCurrencySuffix(): void
    {
        $result = $this->formatter->format(12345.67, 'XYZ');
        $this->assertSame('12 345,67 XYZ', $result);
    }

    /**
     * Testuje formátování číselné hodnoty bez jakéhokoli suffixu.
     * Ověřuje, že hodnota `12345.67` bude správně naformátována jako `12 345,67` bez měny.
     */
    public function testFormatWithoutSuffix(): void
    {
        $result = $this->formatter->format(12345.67);
        $this->assertSame('12 345,67', $result);
    }

    /**
     * Testuje formátování integerové hodnoty.
     * Ověřuje, že hodnota `12345` bude formátována jako `12 345,00`.
     */
    public function testFormatWithIntegerValue(): void
    {
        $result = $this->formatter->format(12345);
        $this->assertSame('12 345,00', $result);
    }

    /**
     * Testuje, jak `NumberFormatter` zpracovává nečíselné hodnoty.
     * Ověřuje, že hodnota `not a number` zůstane nezměněna.
     */
    public function testFormatWithNonNumericValue(): void
    {
        $result = $this->formatter->format('not a number');
        $this->assertSame('not a number', $result);
    }

    /**
     * Testuje formátování číselné hodnoty s měnovým suffixem, který je prefixem.
     * Například `USD` by měl být uveden před částkou.
     * Ověřuje, že hodnota `1000` s měnou `USD` bude `'$ 1 000,00'`.
     */
    public function testFormatWithCurrencySuffixInPrefixFormat(): void
    {
        $result = $this->formatter->format(1000, 'USD');
        $this->assertSame('$ 1 000,00', $result);
    }

    /**
     * Testuje formátování číselné hodnoty s měnovým suffixem, který je umístěn za částkou.
     * Například `CZK` by měl být uveden za částkou.
     * Ověřuje, že hodnota `1000` s měnou `CZK` bude `'1 000,00 Kč'`.
     */
    public function testFormatWithCurrencySuffixInSuffixFormat(): void
    {
        $result = $this->formatter->format(1000, 'CZK');
        $this->assertSame('1 000,00 Kč', $result);
    }
}

