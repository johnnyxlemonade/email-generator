<?php

namespace Lemonade\EmailGenerator\Tests\Collection;

use Lemonade\EmailGenerator\Collection\ProductCollection;
use Lemonade\EmailGenerator\Models\Product;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ProductCollectionInvalidItemTest extends TestCase
{
    public function testAddInvalidItemThrowsException(): void
    {
        // Creating an instance of ProductCollection
        $collection = new ProductCollection();

        // Attempt to add an invalid item (for example, a class object of \stdClass)
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid item type');

        $collection->add(new \stdClass());
    }
}
