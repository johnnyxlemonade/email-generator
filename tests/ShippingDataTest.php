<?php

namespace Lemonade\EmailGenerator\Tests\DTO;

use Lemonade\EmailGenerator\DTO\ShippingData;
use PHPUnit\Framework\TestCase;

class ShippingDataTest extends TestCase
{
    /**
     * Testuje inicializaci třídy ShippingData s výchozími hodnotami.
     */
    public function testInitializationWithDefaultValues(): void
    {
        $shippingData = new ShippingData();

        // Ověřujeme, že výchozí hodnoty odpovídají očekávání
        $this->assertEquals('Výchozí doprava', $shippingData->name);
        $this->assertEquals(0, $shippingData->price);
        $this->assertTrue($shippingData->display);
    }

    /**
     * Testuje inicializaci třídy ShippingData s vlastními hodnotami.
     */
    public function testInitializationWithCustomValues(): void
    {
        $shippingData = new ShippingData(
            name: 'Expresní doprava',
            price: 250.50,
            display: false
        );

        // Ověřujeme, že hodnoty odpovídají těm zadaným při inicializaci
        $this->assertEquals('Expresní doprava', $shippingData->name);
        $this->assertEquals(250.50, $shippingData->price);
        $this->assertFalse($shippingData->display);
    }

    /**
     * Testuje různé datové typy pro cenu (price).
     */
    public function testPriceWithDifferentTypes(): void
    {
        $shippingData = new ShippingData(price: 300);
        $this->assertEquals(300, $shippingData->price);

        $shippingData = new ShippingData(price: 199.99);
        $this->assertEquals(199.99, $shippingData->price);

        $shippingData = new ShippingData(price: 'zdarma');
        $this->assertEquals('zdarma', $shippingData->price);

        $shippingData = new ShippingData(price: null);
        $this->assertNull($shippingData->price);
    }
}
