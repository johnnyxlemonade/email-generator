<?php

namespace Lemonade\EmailGenerator\Collection;

/**
 * Interface ItemCollectionInterface
 * Defines the contract for a collection of items.
 */
interface ItemCollectionInterface
{
    /**
     * Returns all items in the collection.
     *
     * @return array
     */
    public function all(): array;
}