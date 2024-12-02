<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\Collection\AttachmentCollection;
use Lemonade\EmailGenerator\Models\Attachment;
use PHPUnit\Framework\TestCase;

class AttachmentListTest extends TestCase
{
    private AttachmentCollection $attachmentCollection;
    private AttachmentList $attachmentList;

    /**
     * Inicializace AttachmentCollection a AttachmentList před každým testem.
     */
    protected function setUp(): void
    {
        // Vytvoření mocků pro AttachmentCollection a přidání příloh
        $this->attachmentCollection = new AttachmentCollection();
        $attachment1 = $this->createMock(Attachment::class);
        $attachment2 = $this->createMock(Attachment::class);
        $this->attachmentCollection->add($attachment1);
        $this->attachmentCollection->add($attachment2);

        // Vytváříme instanci třídy AttachmentList s předanou kolekcí příloh
        $this->attachmentList = new AttachmentList($this->attachmentCollection);
    }

    /**
     * Testuje inicializaci kontextu s očekávaným klíčem 'attachments'.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->attachmentList, 'context');

        // Ověřujeme, že kontext obsahuje klíč 'attachments' se správnou hodnotou
        $expectedAttachments = $this->attachmentCollection->all();
        $this->assertEquals($expectedAttachments, $context->get('attachments'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadovaný klíč 'attachments'.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->attachmentList);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->attachmentList);

        // Očekáváme, že vše proběhne bez chyby
        $this->expectNotToPerformAssertions();
    }

    /**
     * Pomocná metoda pro získání privátního nebo chráněného vlastnosti z objektu.
     * Používáme reflexi k získání hodnoty vlastnosti z objektu.
     *
     * @param object $object Objekt, ze kterého chceme získat vlastnost.
     * @param string $propertyName Název vlastnosti.
     * @return mixed Hodnota vlastnosti.
     */
    private function getPrivateProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
