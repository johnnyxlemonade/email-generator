<?php

namespace Lemonade\EmailGenerator\Tests\Factories;

use Lemonade\EmailGenerator\Factories\ShippingFactory;
use Lemonade\EmailGenerator\Models\Shipping;
use Lemonade\EmailGenerator\DTO\ShippingData;
use PHPUnit\Framework\TestCase;

class ShippingFactoryTest extends TestCase
{
    /**
     * Testuje, že ShippingFactory správně vytváří instanci Shipping na základě ShippingData.
     */
    public function testCreateShippingFromShippingData(): void
    {
        $shippingData = new ShippingData(
            name: 'Expresní doprava',
            price: 200.75,
            display: true
        );

        $shipping = ShippingFactory::createFromDTO($shippingData);

        // Ověřujeme, že Shipping je správnou instancí
        $this->assertInstanceOf(Shipping::class, $shipping);

        // Ověřujeme, že Shipping obsahuje správné hodnoty z ShippingData
        $this->assertEquals('Expresní doprava', $shipping->getName());
        $this->assertEquals(200.75, $shipping->getPrice());
        $this->assertTrue($shipping->shouldDisplay());
    }
}
