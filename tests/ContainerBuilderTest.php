<?php

namespace Lemonade\EmailGenerator\Tests;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Services\ProductCollectionService;

class ContainerBuilderTest extends TestCase
{
    private LoggerInterface $logger;
    private Translator $translator;
    private TemplateRenderer $templateRenderer;
    private BlockManager $blockManager;
    private ServiceFactoryManager $serviceFactoryManager;
    private ContainerBuilder $containerBuilder;

    protected function setUp(): void
    {
        // Mock dependencies using PHPUnit
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->translator = $this->createMock(Translator::class);
        $this->templateRenderer = $this->createMock(TemplateRenderer::class);
        $this->blockManager = $this->createMock(BlockManager::class);
        $this->serviceFactoryManager = $this->createMock(ServiceFactoryManager::class);

        // Create an instance of ContainerBuilder with mocked dependencies
        $this->containerBuilder = new ContainerBuilder(
            $this->logger,
            $this->translator,
            $this->templateRenderer,
            $this->blockManager,
            $this->serviceFactoryManager
        );
    }

    public function testBasicServicesAccessibility(): void
    {
        // Verify that logger, translator, template renderer, and block manager are accessible
        $this->assertSame($this->logger, $this->containerBuilder->getLogger());
        $this->assertSame($this->translator, $this->containerBuilder->getTranslator());
        $this->assertSame($this->templateRenderer, $this->containerBuilder->getTemplateRenderer());
        $this->assertSame($this->blockManager, $this->containerBuilder->getBlockManager());
    }

    public function testGetProductCollectionService(): void
    {
        // Set up mock for serviceFactoryManager to create ProductCollectionService
        $productCollectionService = $this->createMock(ProductCollectionService::class);
        $this->serviceFactoryManager
            ->expects($this->once()) // This method should be called once
            ->method('createProductCollectionService')
            ->willReturn($productCollectionService);

        // First call to getProductCollectionService should create the service
        $result = $this->containerBuilder->getProductCollectionService();
        $this->assertSame($productCollectionService, $result);

        // Second call to getProductCollectionService should return the same instance
        $result2 = $this->containerBuilder->getProductCollectionService();
        $this->assertSame($result, $result2);
    }

    public function testLazyLoadingOfContextService(): void
    {
        // Set up mock for serviceFactoryManager to create ContextService
        $contextService = $this->createMock(\Lemonade\EmailGenerator\Services\ContextService::class);
        $this->serviceFactoryManager
            ->expects($this->once())
            ->method('createContextService')
            ->willReturn($contextService);

        // First call to getContextService should create the service
        $result = $this->containerBuilder->getContextService();
        $this->assertSame($contextService, $result);

        // Subsequent call should return the same instance
        $result2 = $this->containerBuilder->getContextService();
        $this->assertSame($result, $result2);
    }
}