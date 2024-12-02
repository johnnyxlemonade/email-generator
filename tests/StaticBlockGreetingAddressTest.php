<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Informational;

use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Models\Address;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class StaticBlockGreetingAddressTest extends TestCase
{
    private Address $address;
    private StaticBlockGreetingAddress $staticBlockGreetingAddress;

    /**
     * Inicializace Address a StaticBlockGreetingAddress před každým testem.
     */
    protected function setUp(): void
    {
        // Vytvoření mocku pro Address
        $this->address = $this->createMock(Address::class);

        // Vytváříme instanci třídy StaticBlockGreetingAddress s předanou adresou
        $this->staticBlockGreetingAddress = new StaticBlockGreetingAddress($this->address);
    }

    /**
     * Testuje inicializaci kontextu s očekávaným klíčem 'address'.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->staticBlockGreetingAddress, 'context');

        // Ověřujeme, že kontext obsahuje klíč 'address' se správnou hodnotou
        $this->assertSame($this->address, $context->get('address'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadovaný klíč 'address'.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->staticBlockGreetingAddress);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->staticBlockGreetingAddress);

        // Očekáváme, že vše proběhne bez chyby
        $this->expectNotToPerformAssertions();
    }

    /**
     * Pomocná metoda pro získání privátního nebo chráněného vlastnosti z objektu.
     * Používáme reflexi k získání hodnoty vlastnosti z objektu.
     *
     * @param object $object Objekt, ze kterého chceme získat vlastnost.
     * @param string $propertyName Název vlastnosti.
     * @return mixed Hodnota vlastnosti.
     */
    private function getPrivateProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
