<?php

namespace Tests\Unit\EmailGenerator;

use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use PHPUnit\Framework\TestCase;

class DefaultContainerBuilderTest extends TestCase
{
    public function testCreateReturnsValidContainerBuilder(): void
    {
        // Act: Create the container
        $container = DefaultContainerBuilder::create();

        // Assert: Check the container is an instance of ContainerBuilder
        $this->assertInstanceOf(ContainerBuilder::class, $container);

        // Assert: Check individual components of the container
        $this->assertInstanceOf(FileLogger::class, $container->getLogger());
        $this->assertInstanceOf(Translator::class, $container->getTranslator());
        $this->assertInstanceOf(TemplateRenderer::class, $container->getTemplateRenderer());
        $this->assertInstanceOf(BlockManager::class, $container->getBlockManager());
        $this->assertInstanceOf(ServiceFactoryManager::class, $container->getServiceFactoryManager());
    }

    public function testContainerDependenciesAreProperlyConfigured(): void
    {
        // Act: Create the container
        $container = DefaultContainerBuilder::create();

        // Assert: Check that services within the container are correctly linked
        $blockManager = $container->getBlockManager();
        $this->assertSame($container->getTemplateRenderer(), $blockManager->getTemplateRenderer());
        $this->assertSame($container->getLogger(), $blockManager->getLogger());
        $this->assertSame($container->getTranslator(), $blockManager->getTranslator());
    }
}

