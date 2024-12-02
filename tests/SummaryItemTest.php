<?php

namespace Lemonade\EmailGenerator\Tests\Models;

use Lemonade\EmailGenerator\Models\SummaryItem;
use PHPUnit\Framework\TestCase;

class SummaryItemTest extends TestCase
{
    /**
     * Testuje vytvoření instance `SummaryItem` se všemi hodnotami.
     */
    public function testCreateSummaryItemWithAllValues(): void
    {
        // Vytvoření instance SummaryItem s testovacími daty
        $summaryItem = new SummaryItem(
            name: 'orderId',
            value: '123456',
        );

        // Ověření, že hodnoty v instanci odpovídají očekávání
        $this->assertSame('orderId', $summaryItem->getName());
        $this->assertSame('123456', $summaryItem->getValue());
        $this->assertSame(false, $summaryItem->isFinal());
    }

    /**
     * Testuje vytvoření instance `SummaryItem` bez zadání nepovinné měny (`currency`).
     */
    public function testCreateSummaryItemWithoutCurrency(): void
    {
        // Vytvoření instance SummaryItem bez měny
        $summaryItem = new SummaryItem(
            name: 'orderDate',
            value: '2024-12-01'
        );

        // Ověření, že hodnoty v instanci odpovídají očekávání
        $this->assertSame('orderDate', $summaryItem->getName());
        $this->assertSame('2024-12-01', $summaryItem->getValue());

        // Ověření, že měna je nastavena na final
        $this->assertFalse($summaryItem->isFinal());
    }

}
