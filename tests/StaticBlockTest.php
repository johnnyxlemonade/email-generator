<?php

namespace Lemonade\EmailGenerator\Tests\Blocks;

use Lemonade\EmailGenerator\Blocks\StaticBlock;
use Lemonade\EmailGenerator\Context\ContextData;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Twig\Environment;

class StaticBlockTest extends TestCase
{
    private Environment $twigMock;
    private LoggerInterface $loggerMock;

    protected function setUp(): void
    {
        // Mock Environment
        $this->twigMock = $this->createMock(Environment::class);
        // Mock LoggerInterface
        $this->loggerMock = $this->createMock(LoggerInterface::class);
    }

    public function testRenderBlock()
    {
        // Předpokládáme, že se metoda `render` volá s šablonou a daty.
        $templateName = '@Blocks/StaticBlock.twig';
        $renderedHtml = '<div>Rendered Content</div>';

        $this->twigMock->expects($this->once())
            ->method('render')
            ->with($templateName, [])
            ->willReturn($renderedHtml);

        $staticBlock = new StaticBlock('StaticBlock');
        $result = $staticBlock->renderBlock($this->twigMock, $this->loggerMock);

        // Ověření správnosti
        $this->assertEquals($renderedHtml, $result);
    }
}
