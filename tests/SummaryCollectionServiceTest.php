<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\SummaryFactory;
use Lemonade\EmailGenerator\Models\SummaryItem;
use Lemonade\EmailGenerator\Services\SummaryCollectionService;
use PHPUnit\Framework\TestCase;

class SummaryCollectionServiceTest extends TestCase
{
    private SummaryCollectionService $summaryCollectionService;

    protected function setUp(): void
    {
        $this->summaryCollectionService = new SummaryCollectionService(new SummaryFactory());
    }

    public function testCreateSummaryCollection(): void
    {
        $collection = $this->summaryCollectionService->createCollection();
        $this->assertCount(0, $collection->all());
    }

    public function testAddItemToSummaryCollection(): void
    {
        $collection = $this->summaryCollectionService->createCollection();

        $data = new SummaryData(
            name: "Subtotal",
            value: 100.0,
            final: false
        );

        $this->summaryCollectionService->createItem($collection, $data);
        $this->assertCount(1, $collection->all());

        $summaryItem = $collection->get(0);
        $this->assertInstanceOf(SummaryItem::class, $summaryItem);
        $this->assertEquals("Subtotal", $summaryItem->getName());
        $this->assertEquals(100.0, $summaryItem->getValue());
    }
}
