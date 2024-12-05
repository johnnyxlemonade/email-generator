<?php

namespace Lemonade\EmailGenerator\Services;
use Lemonade\EmailGenerator\Collection\ItemCollectionInterface;

abstract class AbstractCollectionService
{
    /**
     * General logic for retrieving all items in the collection.
     * Ensures that the collection implements ItemCollectionInterface.
     *
     * @param ItemCollectionInterface $collection
     * @return array
     */
    public function getAllItems(ItemCollectionInterface $collection): array
    {
        return $collection->all();
    }
}