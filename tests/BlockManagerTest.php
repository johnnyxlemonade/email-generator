<?php

namespace Lemonade\EmailGenerator\Tests\BlockManager;

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\BlockInterface;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class BlockManagerTest extends TestCase
{
    private BlockManager $blockManager;
    private $templateRenderer;
    private $logger;
    private $translator;

    /**
     * Nastavení prostředí pro každý test - vytváříme mock objekty pro závislosti a inicializujeme BlockManager.
     */
    protected function setUp(): void
    {
        // Mocky pro závislosti třídy BlockManager
        $this->templateRenderer = $this->createMock(TemplateRenderer::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translator = $this->createMock(Translator::class);

        // Vytváříme instanci třídy BlockManager s mocky závislostí
        $this->blockManager = new BlockManager($this->templateRenderer, $this->logger, $this->translator);
    }

    /**
     * Testuje přidání bloku do BlockManageru a ověřuje, že pole bloků není prázdné.
     */
    public function testAddBlock(): void
    {
        $block = $this->createMock(BlockInterface::class);

        $this->blockManager->addBlock($block);

        // Ověřujeme, že pole bloků (`blocks`) není prázdné po přidání jednoho bloku
        $this->assertNotEmpty($this->getPrivateProperty($this->blockManager, 'blocks'));
    }

    /**
     * Testuje nastavení titulku stránky pomocí metody setPageTitle a jeho získání pomocí getPageTitle.
     */
    public function testSetPageTitle(): void
    {
        $title = 'New Page Title';
        $this->blockManager->setPageTitle($title);

        // Ověřujeme, že titulek stránky (`pageTitle`) odpovídá nastavené hodnotě
        $this->assertEquals($title, $this->blockManager->getPageTitle());
    }

    /**
     * Testuje nastavení podporovaného jazyka a ověřuje, že metoda setLanguage v translatoru je volána.
     */
    public function testSetLanguageWithSupportedLanguage(): void
    {
        $language = 'en';
        $this->translator
            ->expects($this->once())
            ->method('setLanguage')
            ->with(SupportedLanguage::LANG_EN);

        $this->blockManager->setLanguage($language);
    }

    /**
     * Testuje nastavení nepodporovaného jazyka a ověřuje, že dojde k zalogování varování.
     */
    public function testSetLanguageWithUnsupportedLanguage(): void
    {
        $language = 'xx';
        $this->logger
            ->expects($this->once())
            ->method('warning')
            ->with('Nepodporovaný jazyk: xx. Ponecháváme výchozí jazyk.');

        $this->blockManager->setLanguage($language);
    }

    /**
     * Testuje nastavení zarovnání na 'center' při vykreslování bloků.
     */
    public function testSetBlockRenderCenter(): void
    {
        $twig = $this->createMock(\Twig\Environment::class);
        $this->templateRenderer
            ->method('getTwig')
            ->willReturn($twig);

        // Ověřujeme, že metoda `addGlobal` je volána se správnými parametry ('alignment', 'center')
        $twig
            ->expects($this->once())
            ->method('addGlobal')
            ->with('alignment', 'center');

        $this->blockManager->setBlockRenderCenter();
    }

    /**
     * Testuje vykreslování HTML pomocí metody getHtml v BlockManageru.
     */
    public function testGetHtml(): void
    {
        // Mock blok, který se přidává do BlockManageru
        $block = $this->createMock(BlockInterface::class);
        $block->expects($this->once())
            ->method('renderBlock')
            ->willReturn('<div>Test Block</div>');

        // Přidáváme blok do BlockManageru
        $this->blockManager->addBlock($block);

        // Očekáváme, že metoda render v TemplateRenderer bude volána s určitými daty
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->with($this->callback(function ($data) {
                return $data['pageTitle'] === 'Default Page Title'
                    && $data['blocks'] === ['<div>Test Block</div>']
                    && $data['language'] === 'cs';
            }))
            ->willReturn('<html>Rendered Content</html>');

        $this->translator
            ->method('getCurrentLanguage')
            ->willReturn('cs');

        // Ověřujeme, že vykreslené HTML odpovídá očekávanému výsledku
        $html = $this->blockManager->getHtml();
        $this->assertEquals('<html>Rendered Content</html>', $html);
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
