<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceDelivery;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\Models\Shipping;
use PHPUnit\Framework\TestCase;

class EccomerceDeliveryTest extends TestCase
{
    private Shipping $shipping;
    private Payment $payment;
    private string $currency;
    private EccomerceDelivery $eccomerceDelivery;

    /**
     * Nastavení prostředí pro každý test - vytváříme mock objekty pro Shipping a Payment a inicializujeme EccomerceDelivery.
     */
    protected function setUp(): void
    {
        // Vytvoření mocků pro Shipping a Payment
        $this->shipping = $this->createMock(Shipping::class);
        $this->payment = $this->createMock(Payment::class);
        $this->currency = "CZK";

        // Vytváříme instanci třídy EccomerceDelivery s mocky Shipping a Payment
        $this->eccomerceDelivery = new EccomerceDelivery($this->shipping, $this->payment, $this->currency);
    }

    /**
     * Testuje inicializaci kontextu se správnými hodnotami pro dopravu a platbu.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->eccomerceDelivery, 'context');

        // Ověřujeme, že kontext obsahuje správné hodnoty pro shipping a payment
        $this->assertSame($this->shipping, $context->get('shipping'));
        $this->assertSame($this->payment, $context->get('payment'));
        $this->assertSame($this->currency, $context->get('currency'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje potřebné klíče.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->eccomerceDelivery);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->eccomerceDelivery);

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