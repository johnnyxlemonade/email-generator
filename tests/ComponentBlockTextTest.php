<?php

namespace Lemonade\EmailGenerator\Tests\Blocks\Component;

use Lemonade\EmailGenerator\Blocks\Component\ComponentBlockText;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;

class ComponentBlockTextTest extends TestCase
{
    private ContextData $context;
    private ComponentBlockText $componentBlockText;

    /**
     * Inicializace ContextData a ComponentBlockText před každým testem.
     */
    protected function setUp(): void
    {
        // Vytvoření instance ContextData s požadovanými klíči
        $this->context = new ContextData([
            'label' => 'Test Label',
            'description' => 'Test Description',
            'content' => 'Test Content'
        ]);

        // Vytváříme instanci třídy ComponentBlockText s předaným kontextem
        $this->componentBlockText = new ComponentBlockText($this->context);
    }

    /**
     * Testuje inicializaci kontextu s očekávanými klíči.
     */
    public function testContextInitialization(): void
    {
        $context = $this->getPrivateProperty($this->componentBlockText, 'context');

        // Ověřujeme, že kontext obsahuje všechny klíče s očekávanými hodnotami
        $this->assertEquals('Test Label', $context->get('label'));
        $this->assertEquals('Test Description', $context->get('description'));
        $this->assertEquals('Test Content', $context->get('content'));
    }

    /**
     * Testuje metodu validateContext - ověřuje, že kontext obsahuje požadované klíče.
     */
    public function testValidateContext(): void
    {
        // Použití reflexe k získání metody validateContext
        $reflection = new \ReflectionClass($this->componentBlockText);
        $method = $reflection->getMethod('validateContext');
        $method->setAccessible(true);

        // Zavoláme chráněnou metodu validateContext
        $method->invoke($this->componentBlockText);

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
