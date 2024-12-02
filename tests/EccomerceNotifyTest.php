<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceNotify;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class EccomerceNotifyTest extends TestCase
{
    private ContextData $context;
    private EccomerceNotify $eccomerceNotify;

    /**
     * Nastavení prostředí pro každý test - inicializujeme ContextData a EccomerceNotify.
     */
    protected function setUp(): void
    {
        // Vytvoření instance kontextu
        $this->context = new ContextData();

        // Přidání očekávaného klíče do kontextu
        $this->context->set("webName", "My Ecommerce Site");

        // Vytváříme instanci třídy EccomerceNotify s předaným kontextem
        $this->eccomerceNotify = new EccomerceNotify($this->context);
    }

    /**
     * Testuje inicializaci kontextu s očekávaným klíčem.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->eccomerceNotify, 'context');

        // Ověřujeme, že kontext obsahuje klíč 'webName' s očekávanou hodnotou
        $this->assertEquals("My Ecommerce Site", $context->get('webName'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadovaný klíč.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->eccomerceNotify);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->eccomerceNotify);

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
