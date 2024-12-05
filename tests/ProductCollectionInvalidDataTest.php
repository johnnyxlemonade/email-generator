<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Models\Product;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ProductCollectionInvalidDataTest extends TestCase
{
    public function testAddInvalidItemToProductCollection(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid item type");

        // Create an instance of ProductCollection
        $collection = new ProductCollection();

        // Adding an invalid type (for example, stdClass instead of Product)
        $invalidItem = new \stdClass();
        $collection->add($invalidItem);
    }
}
