<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceProductList;
use Lemonade\EmailGenerator\Collection\AbstractCollection;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class EccomerceProductListTest extends TestCase
{
    private AbstractCollection $collection;
    private EccomerceProductList $eccomerceProductList;
    private string $currency;

    /**
     * Nastavení prostředí pro každý test - inicializujeme AbstractCollection a EccomerceProductList.
     */
    protected function setUp(): void
    {
        // Vytvoření mocku AbstractCollection
        $this->collection = $this->createMock(AbstractCollection::class);
        $this->collection->method('all')->willReturn(['product1', 'product2', 'product3']);
        $this->currency = "CZK";

        // Vytváříme instanci třídy EccomerceProductList s předanou kolekcí
        $this->eccomerceProductList = new EccomerceProductList($this->collection, $this->currency);
    }

    /**
     * Testuje inicializaci kontextu s očekávaným klíčem 'products'.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->eccomerceProductList, 'context');

        // Ověřujeme, že kontext obsahuje klíč 'products' se správnou hodnotou
        $expectedProducts = ['product1', 'product2', 'product3'];
        $this->assertEquals($expectedProducts, $context->get('products'));
        $this->assertSame($this->currency, $context->get('currency'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadovaný klíč 'products'.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->eccomerceProductList);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->eccomerceProductList);

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
