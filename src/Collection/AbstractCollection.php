<?php

namespace Lemonade\EmailGenerator\Collection;
use InvalidArgumentException;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;

/**
 * Class AbstractCollection
 * Provides a base implementation for a type-safe collection.
 * Enforces validation of items and implements basic collection operations.
 */
abstract class AbstractCollection implements Countable, IteratorAggregate, ItemCollectionInterface
{

    /**
     * @var array Stores items in the collection.
     */
    protected array $items = [];

    /**
     * Adds an item to the collection.
     *
     * @param mixed $item
     * @throws InvalidArgumentException
     */
    public function add($item): void
    {
        if (!$this->validateItem($item)) {
            throw new InvalidArgumentException("Invalid item type");
        }
        $this->items[] = $item;
    }

    /**
     * Returns all items in the collection.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Returns the item at the given index.
     *
     * @param int $index Index of the item.
     * @return mixed Item at the given index.
     * @throws OutOfBoundsException
     */
    public function get(int $index)
    {
        if (!isset($this->items[$index])) {
            throw new OutOfBoundsException("Index {$index} out of bounds.");
        }

        return $this->items[$index];
    }

    /**
     * Returns the number of items in the collection.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Returns an iterator for the collection.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Validates whether the given object is a valid type for the collection.
     *
     * @param mixed $item Object to be validated.
     * @return bool True if valid; otherwise false.
     */
    abstract protected function validateItem($item): bool;
}
