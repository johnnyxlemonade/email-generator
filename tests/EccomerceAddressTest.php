<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceAddress;
use Lemonade\EmailGenerator\Models\Address;
use PHPUnit\Framework\TestCase;

class EccomerceAddressTest extends TestCase
{
    private Address $billingAddress;
    private Address $deliveryAddress;
    private EccomerceAddress $eccomerceAddress;

    /**
     * Nastavení prostředí pro každý test - vytváříme mock objekty pro Address a inicializujeme EccomerceAddress.
     */
    protected function setUp(): void
    {
        // Vytvoření mocků pro Address
        $this->billingAddress = $this->createMock(Address::class);
        $this->deliveryAddress = $this->createMock(Address::class);

        // Vytváříme instanci třídy EccomerceAddress s mocky Address
        $this->eccomerceAddress = new EccomerceAddress($this->billingAddress, $this->deliveryAddress);
    }

    /**
     * Testuje inicializaci kontextu se správnými adresami.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->eccomerceAddress, 'context');

        // Ověřujeme, že kontext obsahuje obě adresy
        $this->assertSame($this->billingAddress, $context->get('billingAddress'));
        $this->assertSame($this->deliveryAddress, $context->get('deliveryAddress'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje potřebné klíče.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->eccomerceAddress);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->eccomerceAddress);

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