<?php

namespace Lemonade\EmailGenerator\Tests\Factories;

use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\Factories\AttachmentFactory;
use Lemonade\EmailGenerator\Models\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentFactoryTest extends TestCase
{
    /**
     * Testuje vytvoření instance `Attachment` pomocí `AttachmentFactory`.
     */
    public function testCreateFromDTO(): void
    {
        // Vytvoření instance AttachmentData s testovacími daty
        $attachmentData = new AttachmentData(
            name: 'Test Attachment',
            link: 'https://example.com/attachment',
            size: '1024',
            extension: 'pdf'
        );

        // Vytvoření instance Attachment pomocí tovární metody
        $attachment = AttachmentFactory::createFromDTO($attachmentData);

        // Ověření, že výsledná instance je typu Attachment
        $this->assertInstanceOf(Attachment::class, $attachment);

        // Ověření, že data v instanci Attachment odpovídají datům z AttachmentData
        $this->assertSame('Test Attachment', $attachment->getName());
        $this->assertSame('https://example.com/attachment', $attachment->getLink());
        $this->assertSame('1024', $attachment->getSize());
        $this->assertSame('pdf', $attachment->getExtension());
    }

    /**
     * Testuje vytvoření instance `Attachment` s neúplnými daty.
     * Tovární metoda musí správně vytvořit instanci i v případě, že některé volitelné údaje chybí.
     */
    public function testCreateFromDTOPartialData(): void
    {
        // Vytvoření instance AttachmentData s pouze základními údaji
        $attachmentData = new AttachmentData(
            name: 'Test Attachment',
            link: 'https://example.com/attachment'
        );

        // Vytvoření instance Attachment pomocí tovární metody
        $attachment = AttachmentFactory::createFromDTO($attachmentData);

        // Ověření, že výsledná instance je typu Attachment
        $this->assertInstanceOf(Attachment::class, $attachment);

        // Ověření, že data v instanci Attachment odpovídají datům z AttachmentData
        $this->assertSame('Test Attachment', $attachment->getName());
        $this->assertSame('https://example.com/attachment', $attachment->getLink());

        // Ověření, že chybějící volitelné údaje jsou nastaveny na null
        $this->assertNull($attachment->getSize());
        $this->assertNull($attachment->getExtension());
    }
}
