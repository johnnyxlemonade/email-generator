<?php

namespace Lemonade\EmailGenerator\Tests\Factories;

use Lemonade\EmailGenerator\Factories\PaymentFactory;
use Lemonade\EmailGenerator\Models\Payment;
use Lemonade\EmailGenerator\DTO\PaymentData;
use PHPUnit\Framework\TestCase;

class PaymentFactoryTest extends TestCase
{
    /**
     * Testuje, že PaymentFactory správně vytváří instanci Payment na základě PaymentData.
     */
    public function testCreatePaymentFromPaymentData(): void
    {
        $paymentData = new PaymentData(
            name: 'Platba kartou',
            price: 100.50,
            display: true
        );

        $payment = PaymentFactory::createFromDTO($paymentData);

        // Ověřujeme, že Payment je správnou instancí
        $this->assertInstanceOf(Payment::class, $payment);

        // Ověřujeme, že Payment obsahuje správné hodnoty z PaymentData
        $this->assertEquals('Platba kartou', $payment->getName());
        $this->assertEquals(100.50, $payment->getPrice());
        $this->assertTrue($payment->shouldDisplay());
    }
}
