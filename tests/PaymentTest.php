<?php

namespace Lemonade\EmailGenerator\Tests\Model;

use Lemonade\EmailGenerator\Models\Payment;
use PHPUnit\Framework\TestCase;

class PaymentTest extends TestCase
{
    /**
     * Testuje základní funkčnost getterů
     *
     * Ověřuje, že po vytvoření instance třídy Payment
     * jsou všechny vlastnosti správně nastaveny a přístupné.
     */
    public function testGetters(): void
    {
        $name = "Platba převodem";
        $price = 100;
        $display = true;

        $payment = new Payment($name, $price, $display);

        $this->assertSame($name, $payment->getName());
        $this->assertSame(100.0, $payment->getPrice());
        $this->assertTrue($payment->shouldDisplay());
    }

    /**
     * Testuje konverzi ceny na float
     *
     * Ověřuje, že hodnota ceny je správně převedena na typ float.
     */
    public function testPriceConversion(): void
    {
        $payment = new Payment("Test Payment", "150");
        $this->assertSame(150.0, $payment->getPrice());

        $payment = new Payment("Test Payment", 200);
        $this->assertSame(200.0, $payment->getPrice());

        $payment = new Payment("Test Payment", 99.99);
        $this->assertSame(99.99, $payment->getPrice());
    }

    /**
     * Testuje situaci, kdy cena platby je null
     *
     * Ověřuje, že pokud není zadána cena platby, vrací se null.
     */
    public function testNullPrice(): void
    {
        $payment = new Payment("Test Payment", null);
        $this->assertNull($payment->getPrice());
    }

    /**
     * Testuje výchozí hodnotu příznaku display
     *
     * Ověřuje, že pokud není zadán příznak display, má výchozí hodnotu true.
     */
    public function testDefaultDisplayValue(): void
    {
        $payment = new Payment("Test Payment", 150, true);
        $this->assertTrue($payment->shouldDisplay());
    }

    /**
     * Testuje příznak display
     *
     * Ověřuje, že příznak display lze nastavit na false a vrací správnou hodnotu.
     */
    public function testCustomDisplayValue(): void
    {
        $payment = new Payment("Test Payment", 150, false);
        $this->assertFalse($payment->shouldDisplay());
    }
}

