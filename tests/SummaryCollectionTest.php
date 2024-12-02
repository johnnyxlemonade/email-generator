<?php

namespace Lemonade\EmailGenerator\Tests\Collection;

use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\Models\SummaryItem;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class SummaryCollectionTest extends TestCase
{
    /**
     * Testuje přidávání položek do kolekce a získání jejich počtu.
     */
    public function testAddItemsToCollection(): void
    {
        $collection = new SummaryCollection();

        $item1 = new SummaryItem('orderId', '123456');
        $item2 = new SummaryItem('orderDate', '2024-12-01');

        // Přidávání položek do kolekce
        $collection->add($item1);
        $collection->add($item2);

        // Ověření, že kolekce má dvě položky
        $this->assertCount(2, $collection);
    }

    /**
     * Testuje, zda metoda `get` vrací správnou položku na daném indexu.
     */
    public function testGetItemFromCollection(): void
    {
        $collection = new SummaryCollection();

        $item1 = new SummaryItem('orderId', '123456');
        $item2 = new SummaryItem('orderDate', '2024-12-01');

        // Přidávání položek do kolekce
        $collection->add($item1);
        $collection->add($item2);

        // Ověření, že se vrací správná položka na indexu 0
        $this->assertSame($item1, $collection->get(0));

        // Ověření, že se vrací správná položka na indexu 1
        $this->assertSame($item2, $collection->get(1));
    }

    /**
     * Testuje výjimku `InvalidArgumentException` při pokusu o přidání neplatného objektu.
     */
    public function testAddInvalidItemToCollection(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $collection = new SummaryCollection();

        // Pokus o přidání neplatného objektu (např. řetězce místo SummaryItem)
        $collection->add('invalid');
    }

    /**
     * Testuje výjimku `OutOfBoundsException` při pokusu o získání položky na neexistujícím indexu.
     */
    public function testGetItemFromInvalidIndex(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        $collection = new SummaryCollection();

        // Přidání jednoho platného objektu do kolekce
        $item1 = new SummaryItem('orderId', '123456');
        $collection->add($item1);

        // Pokus o získání položky na neexistujícím indexu 1
        $collection->get(1);
    }

    /**
     * Testuje, zda metoda `all` vrací všechny položky jako pole.
     */
    public function testAllMethodReturnsAllItems(): void
    {
        $collection = new SummaryCollection();

        $item1 = new SummaryItem('orderId', '123456');
        $item2 = new SummaryItem('orderDate', '2024-12-01');

        // Přidávání položek do kolekce
        $collection->add($item1);
        $collection->add($item2);

        // Ověření, že metoda `all` vrací správné pole položek
        $this->assertSame([$item1, $item2], $collection->all());
    }
}
