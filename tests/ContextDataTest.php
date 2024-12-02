<?php

namespace Lemonade\EmailGenerator\Tests\Context;

use Lemonade\EmailGenerator\Context\ContextData;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ContextDataTest extends TestCase
{
    private ContextData $contextData;

    /**
     * Testuje inicializaci pomocí pole dat.
     */
    public function testInitializationWithData(): void
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $this->contextData = new ContextData($data);

        // Ověřujeme, že kontext obsahuje data zadaná při inicializaci
        $this->assertEquals('value1', $this->contextData->get('key1'));
        $this->assertEquals('value2', $this->contextData->get('key2'));
    }

    /**
     * Testuje metodu set() a get().
     */
    public function testSetAndGet(): void
    {
        $this->contextData = new ContextData();

        // Nastavujeme hodnotu a ověřujeme, že lze správně získat
        $this->contextData->set('key', 'value');
        $this->assertEquals('value', $this->contextData->get('key'));
    }

    /**
     * Testuje metodu validate() s platnými klíči.
     */
    public function testValidateWithValidKeys(): void
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $this->contextData = new ContextData($data);

        // Validace s existujícími klíči by neměla vyhodit žádnou výjimku
        $this->contextData->validate(['key1', 'key2']);
        $this->expectNotToPerformAssertions();
    }

    /**
     * Testuje metodu validate() s chybějícími klíči.
     */
    public function testValidateWithMissingKeysThrowsException(): void
    {
        $data = ['key1' => 'value1'];
        $this->contextData = new ContextData($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Chybějící nebo prázdný požadovaný klíč: 'key2'.");

        // Pokus o validaci chybějícího klíče vyvolá výjimku
        $this->contextData->validate(['key1', 'key2']);
    }

    /**
     * Testuje metodu validate() s prázdnou hodnotou klíče.
     */
    public function testValidateWithEmptyValueThrowsException(): void
    {
        $data = ['key1' => ''];
        $this->contextData = new ContextData($data);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Chybějící nebo prázdný požadovaný klíč: 'key1'.");

        // Pokus o validaci klíče s prázdnou hodnotou vyvolá výjimku
        $this->contextData->validate(['key1']);
    }

    /**
     * Testuje převod dat na asociativní pole pomocí toArray().
     */
    public function testToArray(): void
    {
        $data = ['key1' => 'value1', 'key2' => 'value2'];
        $this->contextData = new ContextData($data);

        // Ověřujeme, že toArray() vrací správné asociativní pole
        $this->assertEquals($data, $this->contextData->toArray());
    }
}
