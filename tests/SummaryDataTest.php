<?php

namespace Lemonade\EmailGenerator\Tests\DTO;

use Lemonade\EmailGenerator\DTO\SummaryData;
use PHPUnit\Framework\TestCase;

class SummaryDataTest extends TestCase
{
    /**
     * Testuje vytvoření instance `SummaryData` se všemi hodnotami.
     */
    public function testCreateSummaryDataWithAllValues(): void
    {
        // Vytvoření instance SummaryData s testovacími daty
        $summaryData = new SummaryData(
            name: 'orderId',
            value: '123456'
        );

        // Ověření, že hodnoty v instanci odpovídají očekávání
        $this->assertSame('orderId', $summaryData->name);
        $this->assertSame('123456', $summaryData->value);
    }

    /**
     * Testuje vytvoření instance `SummaryData` bez zadání nepovinné měny (`currency`).
     */
    public function testCreateSummaryDataWithoutCurrency(): void
    {
        // Vytvoření instance SummaryData bez měny
        $summaryData = new SummaryData(
            name: 'orderDate',
            value: '2024-12-01'
        );

        // Ověření, že hodnoty v instanci odpovídají očekávání
        $this->assertSame('orderDate', $summaryData->name);
        $this->assertSame('2024-12-01', $summaryData->value);

        // Ověření, že měna je nastavena na false
        $this->assertFalse($summaryData->final);
    }
}
