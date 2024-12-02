<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceHeader;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class EccomerceHeaderTest extends TestCase
{
    private ContextData $context;
    private EccomerceHeader $eccomerceHeader;

    /**
     * Nastavení prostředí pro každý test - inicializujeme ContextData a EccomerceHeader.
     */
    protected function setUp(): void
    {
        // Vytvoření instance kontextu
        $this->context = new ContextData();

        // Přidání všech očekávaných klíčů do kontextu
        $this->context->set("orderId", 12345);
        $this->context->set("orderCode", "ORD12345");
        $this->context->set("orderTotal", 250.00);
        $this->context->set("orderCurrency", "USD");
        $this->context->set("orderDate", "2023-12-01");

        // Vytváříme instanci třídy EccomerceOrderInformation s předaným kontextem
        $this->eccomerceHeader = new EccomerceHeader($this->context);
    }

    /**
     * Testuje inicializaci kontextu se všemi potřebnými klíči.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->eccomerceHeader, 'context');

        // Ověřujeme, že kontext obsahuje všechny klíče s očekávanými hodnotami
        $this->assertEquals(12345, $context->get('orderId'));
        $this->assertEquals("ORD12345", $context->get('orderCode'));
        $this->assertEquals(250.00, $context->get('orderTotal'));
        $this->assertEquals("USD", $context->get('orderCurrency'));
        $this->assertEquals("2023-12-01", $context->get('orderDate'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadované klíče.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->eccomerceHeader);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->eccomerceHeader);

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
