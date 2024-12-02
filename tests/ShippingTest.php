<?php

namespace Lemonade\EmailGenerator\Tests\Model;

use Lemonade\EmailGenerator\Models\Shipping;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase
{
    /**
     * Testuje základní funkčnost getterů
     *
     * Ověřuje, že po vytvoření instance třídy Shipping
     * jsou všechny vlastnosti správně nastaveny a přístupné.
     */
    public function testGetters(): void
    {
        $name = "Doručení na adresu";
        $price = 120;
        $display = true;

        $shipping = new Shipping($name, $price, $display);

        $this->assertSame($name, $shipping->getName());
        $this->assertSame(120.0, $shipping->getPrice());
        $this->assertTrue($shipping->shouldDisplay());
    }

    /**
     * Testuje konverzi ceny na float
     *
     * Ověřuje, že hodnota ceny je správně převedena na typ float.
     */
    public function testPriceConversion(): void
    {
        $shipping = new Shipping("Doručení na adresu", "200", "CZK");
        $this->assertSame(200.0, $shipping->getPrice());

        $shipping = new Shipping("Doručení na adresu", 300, "CZK");
        $this->assertSame(300.0, $shipping->getPrice());

        $shipping = new Shipping("Doručení na adresu", 89.99, "CZK");
        $this->assertSame(89.99, $shipping->getPrice());
    }

    /**
     * Testuje situaci, kdy cena dopravy je null
     *
     * Ověřuje, že pokud není zadána cena dopravy, vrací se null.
     */
    public function testNullPrice(): void
    {
        $shipping = new Shipping("Doručení na adresu", null);
        $this->assertNull($shipping->getPrice());
    }

    /**
     * Testuje výchozí hodnotu příznaku display
     *
     * Ověřuje, že pokud není zadán příznak display, má výchozí hodnotu true.
     */
    public function testDefaultDisplayValue(): void
    {
        $shipping = new Shipping("Doručení na adresu", true);
        $this->assertTrue($shipping->shouldDisplay());
    }

    /**
     * Testuje příznak display
     *
     * Ověřuje, že příznak display lze nastavit na false a vrací správnou hodnotu.
     */
    public function testCustomDisplayValue(): void
    {
        $shipping = new Shipping("Doručení na adresu", 150, false);
        $this->assertFalse($shipping->shouldDisplay());
    }
}
