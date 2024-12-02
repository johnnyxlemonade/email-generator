<?php

namespace Lemonade\EmailGenerator\Tests\Model;

use Lemonade\EmailGenerator\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    /**
     * Testuje základní funkčnost getterů
     *
     * Ověřuje, že po vytvoření instance třídy Product
     * jsou všechny vlastnosti správně nastaveny a přístupné.
     */
    public function testGetters(): void
    {
        $product = new Product(
            productId: 1,
            productCode: "P001",
            productName: "Test Product",
            productQuantity: 2,
            productUnitPrice: 100,
            productUnitBase: 90,
            productTax: 21,
            productUrl: "https://example.com/product",
            productImage: "https://example.com/product.jpg",
            productData: ["color" => ["name" => "Barva", "text" => "Červená"]],
            useData: true
        );

        $this->assertSame(1, $product->getId());
        $this->assertSame("P001", $product->getProductCode());
        $this->assertSame("Test Product", $product->getProductName());
        $this->assertSame(2.0, $product->getProductQuantity());
        $this->assertSame(100.0, $product->getProductUnitPrice());
        $this->assertSame(90.0, $product->getProductBasePrice());
        $this->assertSame(21.0, $product->getProductUnitTax());
        $this->assertSame("https://example.com/product", $product->getProductUrl());
        $this->assertSame("https://example.com/product.jpg", $product->getProductImage());
        $this->assertTrue($product->useData());
        $this->assertSame(["color" => ["name" => "Barva", "text" => "Červená"]], $product->getProductData());
    }

    /**
     * Testuje výpočet ceny produktu
     *
     * Ověřuje, že metoda getProductSubtotalPrice správně vrací mezisoučet pro produkt.
     */
    public function testGetProductSubtotalPrice(): void
    {
        $product = new Product(productId: 1, productQuantity: 3, productUnitPrice: 50);

        $this->assertSame(150.0, $product->getProductSubtotalPrice());
    }

    /**
     * Testuje situaci, kdy není zadána cena produktu nebo množství
     *
     * Ověřuje, že pokud není zadána cena nebo množství, metoda getProductSubtotalPrice vrací 0.
     */
    public function testGetProductSubtotalPriceWithMissingValues(): void
    {
        $productWithNoPrice = new Product(productId: 1, productQuantity: 3, productUnitPrice: null);
        $this->assertSame(0.0, $productWithNoPrice->getProductSubtotalPrice());

        $productWithNoQuantity = new Product(productId: 1, productQuantity: null, productUnitPrice: 50);
        $this->assertSame(0.0, $productWithNoQuantity->getProductSubtotalPrice());
    }

    /**
     * Testuje metody pro získávání dat atributů produktu
     *
     * Ověřuje, že metoda getProductDataAttribute vrací správný atribut z pole productData.
     */
    public function testGetProductDataAttributes(): void
    {
        $productData = [
            "color" => ["name" => "Barva", "text" => "Červená"],
            "size" => ["name" => "Velikost", "text" => "M"]
        ];

        $product = new Product(productId: 1, productData: $productData);

        $this->assertSame("Barva", $product->getProductDataName("color"));
        $this->assertSame("Červená", $product->getProductDataText("color"));
        $this->assertSame("Velikost", $product->getProductDataName("size"));
        $this->assertSame("M", $product->getProductDataText("size"));
    }

    /**
     * Testuje metody pro získávání dat atributů, pokud daný atribut neexistuje
     *
     * Ověřuje, že pokud zadaný klíč nebo atribut neexistuje, metoda vrací prázdný řetězec.
     */
    public function testGetProductDataAttributesWithMissingData(): void
    {
        $product = new Product(productId: 1, productData: []);

        $this->assertSame("", $product->getProductDataName("nonexistent"));
        $this->assertSame("", $product->getProductDataText("nonexistent"));
    }
}
