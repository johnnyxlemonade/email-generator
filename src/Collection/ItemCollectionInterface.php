<?php

namespace Lemonade\EmailGenerator\Collection;

interface ItemCollectionInterface
{
    /**
     * Returns all items in the collection.
     *
     * @return array
     */
    public function all(): array;
}