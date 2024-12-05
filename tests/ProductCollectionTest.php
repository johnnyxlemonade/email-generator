<?php

namespace Lemonade\EmailGenerator\Tests\Collection;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Models\Product;
use PHPUnit\Framework\TestCase;
use OutOfBoundsException;
use InvalidArgumentException;

class ProductCollectionTest extends TestCase
{
    private ProductCollection $collection;

    protected function setUp(): void
    {
        $this->collection = new ProductCollection();
    }

    public function testAddValidProduct(): void
    {
        $product = new Product(1, "ABC123", "Product 1", 2, 100.0);
        $this->collection->add($product);

        $this->assertCount(1, $this->collection);
        $this->assertSame([$product], $this->collection->all());
    }

    public function testAddInvalidProductThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->collection->add("Invalid Product");
    }

    public function testGetAllProducts(): void
    {
        $product1 = new Product(1, "ABC123", "Product 1", 2, 100.0);
        $product2 = new Product(2, "DEF456", "Product 2", 3, 200.0);

        $this->collection->add($product1);
        $this->collection->add($product2);

        $this->assertCount(2, $this->collection);
        $this->assertSame([$product1, $product2], $this->collection->all());
    }

    public function testGetProductByIndex(): void
    {
        $product = new Product(1, "ABC123", "Product 1", 2, 100.0);
        $this->collection->add($product);

        $this->assertSame($product, $this->collection->get(0));
    }

    public function testGetProductByInvalidIndexThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->collection->get(0);
    }

    public function testCountProducts(): void
    {
        $this->assertCount(0, $this->collection);

        $product = new Product(1, "ABC123", "Product 1", 2, 100.0);
        $this->collection->add($product);

        $this->assertCount(1, $this->collection);

        $product2 = new Product(2, "DEF456", "Product 2", 3, 200.0);
        $this->collection->add($product2);

        $this->assertCount(2, $this->collection);
    }

    public function testIterationOverCollection(): void
    {
        $product1 = new Product(1, "ABC123", "Product 1", 2, 100.0);
        $product2 = new Product(2, "DEF456", "Product 2", 3, 200.0);

        $this->collection->add($product1);
        $this->collection->add($product2);

        $products = [];
        foreach ($this->collection as $product) {
            $products[] = $product;
        }

        $this->assertSame([$product1, $product2], $products);
    }
}
