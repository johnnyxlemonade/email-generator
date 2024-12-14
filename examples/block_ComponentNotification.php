<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\ComponentNotification;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LogLevel;

require __DIR__ . '/../vendor/autoload.php';

// 1. Creating basic mandatory services
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::WARNING)); // Logger service
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_EN, logger: $logger); // Translator service
$templateRenderer = new TemplateRenderer(logger: $logger, translator: $translator); // Template rendering service
$blockManager = new BlockManager(templateRenderer: $templateRenderer, logger: $logger, translator: $translator); // Block manager for managing email content
$serviceManager = new ServiceFactoryManager(); // Service factory manager to create various services

// 2. Initializing ContainerBuilder with context
$container = new ContainerBuilder(
    logger: $logger,
    translator: $translator,
    templateRenderer: $templateRenderer,
    blockManager: $blockManager,
    serviceFactoryManager: $serviceManager
);

// 3. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "ComponentNotification"); // Set page title

$heading = "What is Lorem Ipsum?";
$notification = "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. \n \n It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum";

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(new ComponentNotification(contextService: $contextService, heading: $heading, notification: $notification)); // Notification block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
