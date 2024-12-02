<?php

namespace Lemonade\EmailGenerator\Tests\Localization;

use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class TranslatorTest extends TestCase
{
    private LoggerInterface $logger;

    /**
     * Nastaví testovací prostředí před každým testem.
     */
    protected function setUp(): void
    {
        // Vytváříme mock LoggerInterface
        $this->logger = $this->createMock(LoggerInterface::class);
    }

    /**
     * Testuje správné nastavení aktuálního jazyka.
     */
    public function testSetLanguage(): void
    {
        // Vytváříme instanci Translator
        $translator = new Translator($this->logger);

        // Nastavení jazyka na angličtinu
        $translator->setLanguage(SupportedLanguage::LANG_EN);
        $this->assertEquals('en', $translator->getCurrentLanguage());

        // Nastavení jazyka zpět na výchozí (čeština)
        $translator->setLanguage(SupportedLanguage::LANG_CS);
        $this->assertEquals('cs', $translator->getCurrentLanguage());
    }

    /**
     * Testuje, že metoda translate() vrací správné překlady nebo výchozí klíče.
     */
    public function testTranslate(): void
    {
        // Mockování Translatoru, abychom mohli mockovat veřejnou metodu loadDictionaryFromFile()
        $translatorMock = $this->getMockBuilder(Translator::class)
            ->setConstructorArgs([$this->logger])
            ->onlyMethods(['loadDictionaryFromFile'])
            ->getMock();

        // Mockování metody loadDictionaryFromFile tak, aby vrátila konkrétní překlad pro každý jazyk
        $translatorMock->method('loadDictionaryFromFile')
            ->willReturn([
                'greetingHeader' => 'Dobrý den',
            ]);

        // Nastavení jazyka na angličtinu, kde překlad neexistuje a měl by se použít fallback
        $translatorMock->setLanguage(SupportedLanguage::LANG_EN);

        // Překlad klíče, který neexistuje v angličtině, ale existuje v českém slovníku
        $translation = $translatorMock->translate('greetingHeader');
        $this->assertEquals('Dobrý den', $translation);
    }

    /**
     * Testuje, že metoda translate() správně interpoluje hodnoty do překladů.
     */
    public function testTranslateWithInterpolation(): void
    {
        // Vytváříme instanci Translator
        $translator = new Translator($this->logger);

        // Nastavení jazyka na češtinu
        $translator->setLanguage(SupportedLanguage::LANG_CS);

        // Překlad s parametry pro interpolaci
        $translation = $translator->translate('lostPassHead', ['webName' => 'Můj Web']);
        $this->assertStringContainsString('Můj Web', $translation);
        $this->assertStringContainsString('obdrželi jsme žádost o vytvoření nového hesla', $translation);
    }
}
