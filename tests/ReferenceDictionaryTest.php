<?php

namespace Lemonade\EmailGenerator\Tests\Localization;

use Lemonade\EmailGenerator\Localization\ReferenceDictionary;
use PHPUnit\Framework\TestCase;

class ReferenceDictionaryTest extends TestCase
{
    /**
     * Testuje, že metoda getKeys() vrací všechny klíče z referenčního slovníku.
     */
    public function testGetKeys(): void
    {
        $keys = ReferenceDictionary::getKeys();

        // Ověřujeme, že metoda vrací pole
        $this->assertIsArray($keys);

        // Kontrola, zda pole obsahuje specifické klíče
        $this->assertContains('addressLabel', $keys);
        $this->assertContains('orderNumber', $keys);
        $this->assertContains('greetingFooter', $keys);
    }

    /**
     * Testuje, že metoda getDictionary() vrací celý referenční slovník.
     */
    public function testGetDictionary(): void
    {
        $dictionary = ReferenceDictionary::getDictionary();

        // Ověřujeme, že slovník je asociativní pole
        $this->assertIsArray($dictionary);

        // Ověřujeme, že slovník obsahuje specifické klíče s jejich překlady
        $this->assertArrayHasKey('addressLabel', $dictionary);
        $this->assertEquals('Zákaznická data', $dictionary['addressLabel']);

        $this->assertArrayHasKey('greetingCustomer', $dictionary);
        $this->assertEquals('Dobrý den,', $dictionary['greetingCustomer']);
    }

    /**
     * Testuje, že metoda getDefaultTranslation() vrací správný překlad pro daný klíč.
     */
    public function testGetDefaultTranslation(): void
    {
        $translation = ReferenceDictionary::getDefaultTranslation('greetingHeader');

        // Ověřujeme, že překlad pro klíč 'greetingHeader' je správný
        $this->assertEquals('Dobrý den', $translation);

        // Testujeme, že pokud klíč neexistuje, vrací se null
        $nonExistentTranslation = ReferenceDictionary::getDefaultTranslation('nonExistentKey');
        $this->assertNull($nonExistentTranslation);
    }
}
