<?php

namespace Lemonade\EmailGenerator\Collection;

use Lemonade\EmailGenerator\Models\Product;

/**
 * Class ProductCollection
 * Represents a collection of Product objects.
 */
class ProductCollection extends AbstractCollection
{
    /**
     * Validates whether the given object is a valid type for the collection (in this case, an instance of Product).
     *
     * @param mixed $item The object being validated.
     * @return bool True if the object is valid (an instance of Product); otherwise false.
     */
    protected function validateItem($item): bool
    {
        return $item instanceof Product;
    }
}