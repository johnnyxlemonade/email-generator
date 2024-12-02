<?php

namespace Lemonade\EmailGenerator\Tests\Factories;

use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\SummaryFactory;
use Lemonade\EmailGenerator\Models\SummaryItem;
use PHPUnit\Framework\TestCase;

class SummaryFactoryTest extends TestCase
{
    /**
     * Testuje vytvoření instance `SummaryItem` pomocí `SummaryFactory`.
     */
    public function testCreateFromDTO(): void
    {
        // Vytvoření instance SummaryData s testovacími daty
        $summaryData = new SummaryData(
            name: 'orderId',
            value: '123456',
            final: false
        );

        // Vytvoření instance SummaryItem pomocí tovární metody
        $summaryItem = SummaryFactory::createFromDTO($summaryData);

        // Ověření, že výsledná instance je typu SummaryItem
        $this->assertInstanceOf(SummaryItem::class, $summaryItem);

        // Ověření, že data v instanci SummaryItem odpovídají datům z SummaryData
        $this->assertSame('orderId', $summaryItem->getName());
        $this->assertSame('123456', $summaryItem->getValue());
        $this->assertFalse($summaryItem->isFinal());
    }

    /**
     * Testuje vytvoření instance `SummaryItem` s chybějící měnou (`currency`).
     * Zajišťuje, že měna je nastavena na `null`, pokud není uvedena v `SummaryData`.
     */
    public function testCreateFromDTOPartialData(): void
    {
        // Vytvoření instance SummaryData bez měny (currency)
        $summaryData = new SummaryData(
            name: 'orderId',
            value: '123456'
        );

        // Vytvoření instance SummaryItem pomocí tovární metody
        $summaryItem = SummaryFactory::createFromDTO($summaryData);

        // Ověření, že výsledná instance je typu SummaryItem
        $this->assertInstanceOf(SummaryItem::class, $summaryItem);

        // Ověření, že data v instanci SummaryItem odpovídají datům z SummaryData
        $this->assertSame('orderId', $summaryItem->getName());
        $this->assertSame('123456', $summaryItem->getValue());

        // Ověření, že chybějící měna je nastavena na `false`
        $this->assertFalse($summaryItem->isFinal());
    }
}
