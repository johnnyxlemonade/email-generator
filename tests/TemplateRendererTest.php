<?php

namespace Lemonade\EmailGenerator\Tests\Template;

use Lemonade\EmailGenerator\Template\TemplateRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\FilesystemLoader;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\Localization\Translator;

class TemplateRendererTest extends TestCase
{
    private TemplateRenderer $templateRenderer;
    private $logger;
    private $translator;

    /**
     * Nastavení prostředí pro každý test - vytváříme mock objekty pro závislosti a inicializujeme TemplateRenderer.
     */
    protected function setUp(): void
    {
        // Mocky pro logger a translator, které jsou závislostmi TemplateRendereru
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translator = $this->createMock(Translator::class);

        // Vytváříme instanci TemplateRenderer s mocky závislostí
        $this->templateRenderer = new TemplateRenderer($this->logger, $this->translator);
    }

    /**
     * Testuje, že metoda render vrátí správný obsah při úspěšném vykreslení šablony.
     */
    public function testRender(): void
    {
        // Připravujeme očekávaná data a voláme render metodu
        $result = $this->templateRenderer->render([
            'pageTitle' => 'Test Page',
            'blocks' => ['<div>Test Block</div>'],
            'language' => 'en',
        ]);

        // Ověřujeme, že metoda render vrací správný obsah
        $this->assertStringContainsString('<!doctype html>', $result);
        $this->assertStringContainsString('Test Page', $result);
    }

    /**
     * Testuje chování metody render při výjimce a ověřuje, že se zaloguje chyba.
     */
    public function testRenderWithError(): void
    {
        // Očekáváme, že metoda 'error' v loggeru bude zavolána, pokud dojde k výjimce
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with($this->stringContains('Chyba při vykreslování šablony'));

        // Nahrazujeme instanci Twig loaderu mockem, který vyhodí výjimku při volání metody render
        $twigMock = $this->createMock(Environment::class);
        $twigMock->expects($this->once())
            ->method('render')
            ->willThrowException(new \Exception('Simulovaná chyba při vykreslování'));

        // Nastavíme mock místo skutečného Twig prostředí pomocí reflexe
        $reflection = new \ReflectionClass(TemplateRenderer::class);
        $property = $reflection->getProperty('twig');
        $property->setAccessible(true);
        $property->setValue($this->templateRenderer, $twigMock);

        // Volání render metody a ověření, že se vrátí správná chybová zpráva
        $result = $this->templateRenderer->render([]);
        $this->assertEquals("Omlouváme se, došlo k chybě při vykreslování.", $result);
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
