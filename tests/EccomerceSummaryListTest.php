<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Order;

use Lemonade\EmailGenerator\Blocks\Order\EccomerceSummaryList;
use Lemonade\EmailGenerator\Collection\SummaryCollection;
use Lemonade\EmailGenerator\Models\SummaryItem;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class EccomerceSummaryListTest extends TestCase
{
    /**
     * Testuje vytvoření instance `EccomerceSummaryList` se správným kontextem.
     */
    public function testConstructorSetsContextProperly(): void
    {
        // Vytvoření kolekce SummaryCollection a přidání několika položek
        $collection = new SummaryCollection();
        $collection->add(new SummaryItem('orderId', '123456'));
        $collection->add(new SummaryItem('orderDate', '2024-12-01'));

        // Vytvoření instance EccomerceSummaryList
        $orderSummaryList = new EccomerceSummaryList($collection, "CZK");

        // Získání kontextu z EccomerceSummaryList
        $context = $orderSummaryList->getContext();

        // Ověření, že kontext obsahuje správné údaje
        $this->assertInstanceOf(ContextData::class, $context);
        $this->assertArrayHasKey('summary', $context->toArray());
        $this->assertArrayHasKey('currency', $context->toArray());
        $this->assertCount(2, $context->get('summary'));
    }

    /**
     * Testuje, zda metoda `validateContext` správně ověřuje, že klíč `summary` je nastaven v kontextu.
     */
    public function testValidateContext(): void
    {
        // Vytvoření kolekce SummaryCollection a přidání jedné položky
        $collection = new SummaryCollection();
        $collection->add(new SummaryItem('orderId', '123456'));

        // Vytvoření instance EccomerceSummaryList
        $orderSummaryList = new EccomerceSummaryList($collection, "CZK");

        // Použití reflektoru k zpřístupnění `validateContext` metody
        $reflection = new \ReflectionClass(EccomerceSummaryList::class);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavolání chráněné metody
        $this->expectNotToPerformAssertions();
        $method->invoke($orderSummaryList);
    }


}
