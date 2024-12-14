<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\AttachmentData;
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

// 3. Attachments
$attachmentService     = $container->getAttachmentCollectionService(); // Attachment service
$attachmentCollection  = $attachmentService->createCollection(); // Attachment collection

// 4. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "AttachmentList"); // Set page title

// Adding attachment to the collection
for ($i = 1; $i <= 3; $i++) {

    $attachmentData = new AttachmentData(
        name: 'AttachmentItem ' . $i,
        link: 'https://google.com',
        size: '1MB',
        extension: 'pdf'
    );

    $attachmentService->createItem(collection: $attachmentCollection, data: $attachmentData);
}

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(block: new AttachmentList(collection: $attachmentCollection)); // Attachments list block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
