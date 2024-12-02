<?php

namespace Lemonade\EmailGenerator\Tests\Collection;

use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Models\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentCollectionTest extends TestCase
{
    private AttachmentCollection $attachmentCollection;

    /**
     * Inicializace AttachmentCollection před každým testem.
     */
    protected function setUp(): void
    {
        $this->attachmentCollection = new AttachmentCollection();
    }

    /**
     * Testuje přidání platné instance Attachment do kolekce.
     */
    public function testAddValidAttachment(): void
    {
        $attachment = $this->createMock(Attachment::class);

        // Přidáváme přílohu do kolekce
        $this->attachmentCollection->add($attachment);

        // Ověřujeme, že kolekce obsahuje přidanou přílohu
        $this->assertCount(1, $this->attachmentCollection);

        // Ověřujeme, že přidaná příloha je v kolekci na správném indexu
        $this->assertSame($attachment, $this->attachmentCollection->get(0));
    }

    /**
     * Testuje přidání neplatného objektu do kolekce.
     */
    public function testAddInvalidItemThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        // Přidáváme neplatný objekt (který není instancí Attachment)
        $this->attachmentCollection->add("Invalid Item");
    }

    /**
     * Testuje přístup k neplatnému indexu pomocí metody get().
     */
    public function testGetInvalidIndexThrowsException(): void
    {
        $this->expectException(\OutOfBoundsException::class);

        // Pokus o získání položky na neplatném indexu vyvolá výjimku
        $this->attachmentCollection->get(1);
    }

    /**
     * Testuje, zda metoda validateItem vrací true pro instanci Attachment.
     */
    public function testValidateItemWithValidAttachment(): void
    {
        $attachment = $this->createMock(Attachment::class);

        // Použití reflexe pro přístup k chráněné metodě validateItem
        $reflection = new \ReflectionClass($this->attachmentCollection);
        $method = $reflection->getMethod('validateItem');
        $method->setAccessible(true);

        // Ověření, že validateItem vrací true pro instanci Attachment
        $this->assertTrue($method->invoke($this->attachmentCollection, $attachment));
    }

    /**
     * Testuje, zda metoda validateItem vrací false pro neplatný objekt.
     */
    public function testValidateItemWithInvalidItem(): void
    {
        $invalidItem = "Not an Attachment";

        // Použití reflexe pro přístup k chráněné metodě validateItem
        $reflection = new \ReflectionClass($this->attachmentCollection);
        $method = $reflection->getMethod('validateItem');
        $method->setAccessible(true);

        // Ověření, že validateItem vrací false pro neplatný objekt
        $this->assertFalse($method->invoke($this->attachmentCollection, $invalidItem));
    }
}
