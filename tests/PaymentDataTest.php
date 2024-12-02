<?php

namespace Lemonade\EmailGenerator\Tests\DTO;

use Lemonade\EmailGenerator\DTO\PaymentData;
use PHPUnit\Framework\TestCase;

class PaymentDataTest extends TestCase
{
    /**
     * Testuje inicializaci třídy PaymentData s výchozími hodnotami.
     */
    public function testInitializationWithDefaultValues(): void
    {
        $paymentData = new PaymentData();

        // Ověřujeme, že výchozí hodnoty odpovídají očekávání
        $this->assertEquals('Výchozí platba', $paymentData->name);
        $this->assertEquals(0, $paymentData->price);
        $this->assertTrue($paymentData->display);
    }

    /**
     * Testuje inicializaci třídy PaymentData s vlastními hodnotami.
     */
    public function testInitializationWithCustomValues(): void
    {
        $paymentData = new PaymentData(
            name: 'Online platba',
            price: 150.75,
            display: false
        );

        // Ověřujeme, že hodnoty odpovídají těm zadaným při inicializaci
        $this->assertEquals('Online platba', $paymentData->name);
        $this->assertEquals(150.75, $paymentData->price);
        $this->assertFalse($paymentData->display);
    }

    /**
     * Testuje různé datové typy pro cenu (price).
     */
    public function testPriceWithDifferentTypes(): void
    {
        $paymentData = new PaymentData(price: 200);
        $this->assertEquals(200, $paymentData->price);

        $paymentData = new PaymentData(price: 199.99);
        $this->assertEquals(199.99, $paymentData->price);

        $paymentData = new PaymentData(price: 'free');
        $this->assertEquals('free', $paymentData->price);

        $paymentData = new PaymentData(price: null);
        $this->assertNull($paymentData->price);
    }
}
