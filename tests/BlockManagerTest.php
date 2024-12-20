<?php

namespace Lemonade\EmailGenerator\Tests;

use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use PHPUnit\Framework\TestCase;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\StaticBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\Localization\Translator;

class BlockManagerTest extends TestCase
{
    private BlockManager $blockManager;
    private TemplateRenderer $templateRenderer;
    private LoggerInterface $logger;
    private Translator $translator;

    protected function setUp(): void
    {
        // Mocking dependencies
        $this->templateRenderer = $this->createMock(TemplateRenderer::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translator = $this->createMock(Translator::class);

        // Creating an instance of BlockManager with mocked dependencies
        $this->blockManager = new BlockManager(
            $this->templateRenderer,
            $this->logger,
            $this->translator
        );
    }

    public function testAddAndRenderBlocks(): void
    {
        // Creating mock blocks to add to BlockManager
        $block1 = $this->createMock(StaticBlock::class);
        $block2 = $this->createMock(StaticBlock::class);

        // Mocking renderBlock for the blocks
        $block1->expects($this->once())
            ->method('renderBlock')
            ->willReturn('Rendered Block 1');

        $block2->expects($this->once())
            ->method('renderBlock')
            ->willReturn('Rendered Block 2');

        // Adding blocks to BlockManager
        $this->blockManager->addBlock($block1);
        $this->blockManager->addBlock($block2);

        // Mock render behavior of the template renderer
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($data) {
                $this->assertContains('Rendered Block 1', $data['blocks']);
                $this->assertContains('Rendered Block 2', $data['blocks']);
                return 'Rendered HTML Content';
            });

        // Testing the final HTML rendering
        $html = $this->blockManager->getHtml();
        $this->assertEquals('Rendered HTML Content', $html);
    }

    public function testSetPageTitle(): void
    {
        // Setting page title
        $this->blockManager->setPageTitle('Test Page Title');

        // Verifying the page title
        $this->assertEquals('Test Page Title', $this->blockManager->getPageTitle());
    }


    public function testRenderBlock(): void
    {
        // Create a mock block
        $block = $this->createMock(\Lemonade\EmailGenerator\Blocks\BlockInterface::class);
        $block
            ->expects($this->once())
            ->method('renderBlock')
            ->with($this->templateRenderer->getTwig(), $this->logger)
            ->willReturn('<div>Test Block Content</div>');

        // Add the block to the BlockManager
        $this->blockManager->addBlock($block);

        // Set expectations for TemplateRenderer to render with a given set of data
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->with($this->arrayHasKey('blocks'))
            ->willReturn('<html><body>Rendered Template with Block</body></html>');

        // Invoke the rendering of the entire HTML
        $renderedHtml = $this->blockManager->getHtml();

        // Assert that the rendered HTML contains the expected content
        $this->assertStringContainsString('Rendered Template with Block', $renderedHtml);
    }

    public function testDynamicContextUpdate(): void
    {
        // Create a mock block and add it to BlockManager
        $block = $this->createMock(\Lemonade\EmailGenerator\Blocks\AbstractBlock::class);
        $this->blockManager->addBlock($block);

        // Set initial page title
        $this->blockManager->setPageTitle("Initial Page Title");

        // Assert that getPageTitle returns the correct initial title
        $this->assertSame("Initial Page Title", $this->blockManager->getPageTitle());

        // Update the page title dynamically
        $this->blockManager->setPageTitle("Updated Page Title");

        // Assert that getPageTitle returns the updated title
        $this->assertSame("Updated Page Title", $this->blockManager->getPageTitle());

    }

    public function testBlockRenderAlignmentChange(): void
    {
        // Mock the Environment and check that the global 'alignment' is updated correctly
        $twigEnvironment = $this->createMock(\Twig\Environment::class);
        $twigEnvironment
            ->expects($this->once())
            ->method('addGlobal')
            ->with('alignment', 'center');

        // Mock TemplateRenderer to return the mocked Environment
        $this->templateRenderer
            ->method('getTwig')
            ->willReturn($twigEnvironment);

        // Call the method that should change the alignment
        $this->blockManager->setBlockRenderCenter();
    }

    public function testEmptyBlocksScenario(): void
    {
        // Mock render behavior of the template renderer when no blocks are present
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($data) {
                $this->assertEmpty($data['blocks']);
                return 'Rendered HTML Content Without Blocks';
            });

        // Test the final HTML rendering when no blocks are added
        $html = $this->blockManager->getHtml();
        $this->assertEquals('Rendered HTML Content Without Blocks', $html);
    }


    public function testHandlingMultipleBlocks(): void
    {
        // Creating mock blocks of different types
        $block1 = $this->createMock(StaticBlock::class);
        $block2 = $this->createMock(\Lemonade\EmailGenerator\Blocks\Component\AttachmentList::class);

        // Mocking the renderBlock method for each block
        $block1->expects($this->once())
            ->method('renderBlock')
            ->willReturn('Rendered Static Block');

        $block2->expects($this->once())
            ->method('renderBlock')
            ->willReturn('Rendered Attachment List Block');

        // Adding blocks to the BlockManager
        $this->blockManager->addBlock($block1);
        $this->blockManager->addBlock($block2);

        // Mocking render behavior of the TemplateRenderer
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($data) {
                $this->assertContains('Rendered Static Block', $data['blocks']);
                $this->assertContains('Rendered Attachment List Block', $data['blocks']);
                return 'Rendered HTML Content With Multiple Blocks';
            });

        // Testing the final HTML
        $html = $this->blockManager->getHtml();
        $this->assertEquals('Rendered HTML Content With Multiple Blocks', $html);
    }

    public function testRenderingWithNoBlocks(): void
    {
        // Mocking render behavior of the TemplateRenderer
        $this->templateRenderer
            ->expects($this->once())
            ->method('render')
            ->willReturnCallback(function ($data) {
                // Verify that the blocks array is empty
                $this->assertEmpty($data['blocks']);
                return 'Rendered HTML Content Without Blocks';
            });

        // Testing the final HTML
        $html = $this->blockManager->getHtml();
        $this->assertEquals('Rendered HTML Content Without Blocks', $html);
    }



}
