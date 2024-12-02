<?php

namespace Lemonade\EmailGenerator\Tests\Model;

use Lemonade\EmailGenerator\Models\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentTest extends TestCase
{
    /**
     * Testuje základní funkčnost getterů
     *
     * Ověřuje, že po vytvoření instance třídy Attachment
     * jsou všechny vlastnosti správně nastaveny a přístupné.
     */
    public function testGetters(): void
    {
        $name = "Test Attachment";
        $link = "https://example.com/test.pdf";
        $size = "12345";
        $extension = "pdf";

        $attachment = new Attachment($name, $link, $size, $extension);

        $this->assertSame($name, $attachment->getName());
        $this->assertSame($link, $attachment->getLink());
        $this->assertSame($size, $attachment->getSize());
        $this->assertSame($extension, $attachment->getExtension());
    }

    /**
     * Testuje metodu getDescription s velikostí a příponou
     *
     * Ověřuje, že pokud jsou předány obě volitelné hodnoty (size a extension),
     * metoda getDescription správně vytvoří popis.
     */
    public function testGetDescriptionWithSizeAndExtension(): void
    {
        $attachment = new Attachment("Test Attachment", "https://example.com/test.pdf", "12345", "pdf");

        $expectedDescription = "pdf, 12345";
        $this->assertSame($expectedDescription, $attachment->getDescription());
    }

    /**
     * Testuje metodu getDescription bez velikosti
     *
     * Ověřuje, že pokud není předána velikost přílohy, popis obsahuje jen příponu.
     */
    public function testGetDescriptionWithoutSize(): void
    {
        $attachment = new Attachment("Test Attachment", "https://example.com/test.pdf", null, "pdf");

        $expectedDescription = "pdf";
        $this->assertSame($expectedDescription, $attachment->getDescription());
    }

    /**
     * Testuje metodu getDescription bez přípony
     *
     * Ověřuje, že pokud není předána přípona souboru, popis obsahuje jen velikost.
     */
    public function testGetDescriptionWithoutExtension(): void
    {
        $attachment = new Attachment("Test Attachment", "https://example.com/test.pdf", "12345", null);

        $expectedDescription = "12345";
        $this->assertSame($expectedDescription, $attachment->getDescription());
    }

    /**
     * Testuje metodu getDescription bez velikosti a přípony
     *
     * Ověřuje, že pokud nejsou předány ani velikost, ani přípona,
     * metoda getDescription vrátí prázdný řetězec.
     */
    public function testGetDescriptionWithoutSizeAndExtension(): void
    {
        $attachment = new Attachment("Test Attachment", "https://example.com/test.pdf");

        $expectedDescription = "";
        $this->assertSame($expectedDescription, $attachment->getDescription());
    }
}

