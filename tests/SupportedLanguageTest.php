<?php

namespace Lemonade\EmailGenerator\Tests\Localization;

use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use PHPUnit\Framework\TestCase;

class SupportedLanguageTest extends TestCase
{
    /**
     * Testuje, že všechny jazykové konstanty jsou správně deklarovány.
     */
    public function testSupportedLanguagesExist(): void
    {
        // Seznam očekávaných jazykových kódů
        $expectedLanguages = [
            'cs', 'de', 'en', 'es', 'fi', 'fr', 'gr', 'hu',
            'it', 'no', 'pl', 'pt', 'ro', 'ru', 'se', 'ua', 'sk'
        ];

        // Ověřujeme, že všechny jazyky jsou deklarovány
        foreach ($expectedLanguages as $languageCode) {
            $languageEnum = SupportedLanguage::tryFrom($languageCode);
            $this->assertNotNull($languageEnum, "Jazykový kód '$languageCode' nebyl nalezen v enumu SupportedLanguage.");
        }
    }

    /**
     * Testuje přístup k jednotlivým jazykovým konstantám.
     */
    public function testLanguageValues(): void
    {
        // Ověřujeme, že enumy vracejí správné hodnoty
        $this->assertEquals('cs', SupportedLanguage::LANG_CS->value);
        $this->assertEquals('en', SupportedLanguage::LANG_EN->value);
        $this->assertEquals('fr', SupportedLanguage::LANG_FR->value);
    }
}
