<?php

namespace Lemonade\EmailGenerator\Collection;
use InvalidArgumentException;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;

abstract class AbstractCollection implements Countable, IteratorAggregate
{

    /**
     * @var array Skladování prvků kolekce
     */
    protected array $items = [];

    /**
     * Přidává položku do kolekce.
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
     * Vrací všechny položky kolekce.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Vrací položku na zadaném indexu.
     *
     * @param int $index Index položky.
     * @return mixed Položka na zadaném indexu.
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
     * Vrací počet položek v kolekci.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Vrací iterátor pro kolekci.
     *
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Ověří, zda je daný objekt platným typem pro kolekci.
     *
     * @param mixed $item Objekt, který se ověřuje.
     * @return bool True, pokud je platný; jinak false.
     */
    abstract protected function validateItem($item): bool;

}
