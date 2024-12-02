<?php

namespace Lemonade\EmailGenerator\Tests\Collection;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductCollectionTest extends TestCase
{
    private ProductCollection $productCollection;

    /**
     * Inicializace ProductCollection před každým testem.
     */
    protected function setUp(): void
    {
        $this->productCollection = new ProductCollection();
    }

    /**
     * Testuje přidání platné instance Product do kolekce.
     */
    public function testAddValidProduct(): void
    {
        $product = $this->createMock(Product::class);

        // Přidáváme produkt do kolekce
        $this->productCollection->add($product);

        // Ověřujeme, že kolekce obsahuje přidaný produkt
        $this->assertCount(1, $this->productCollection);

        // Ověřujeme, že přidaný produkt je v kolekci na správném indexu
        $this->assertSame($product, $this->productCollection->get(0));
    }

    /**
     * Testuje přidání neplatného objektu do kolekce.
     */
    public function testAddInvalidItemThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Přidáváme neplatný objekt (který není instancí Product)
        $this->productCollection->add("Invalid Item");
    }

    /**
     * Testuje přístup k neplatnému indexu pomocí metody get().
     */
    public function testGetInvalidIndexThrowsException(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        // Pokus o získání položky na neplatném indexu vyvolá výjimku
        $this->productCollection->get(1);
    }

    /**
     * Testuje, zda metoda validateItem vrací true pro instanci Product.
     */
    public function testValidateItemWithValidProduct(): void
    {
        $product = $this->createMock(Product::class);

        // Použití reflexe pro přístup k chráněné metodě validateItem
        $reflection = new \ReflectionClass($this->productCollection);
        $method = $reflection->getMethod('validateItem');
        $method->setAccessible(true);

        // Ověření, že validateItem vrací true pro instanci Product
        $this->assertTrue($method->invoke($this->productCollection, $product));
    }

    /**
     * Testuje, zda metoda validateItem vrací false pro neplatný objekt.
     */
    public function testValidateItemWithInvalidItem(): void
    {
        $invalidItem = "Not a Product";

        // Použití reflexe pro přístup k chráněné metodě validateItem
        $reflection = new \ReflectionClass($this->productCollection);
        $method = $reflection->getMethod('validateItem');
        $method->setAccessible(true);

        // Ověření, že validateItem vrací false pro neplatný objekt
        $this->assertFalse($method->invoke($this->productCollection, $invalidItem));
    }
}
