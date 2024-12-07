<?php

use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
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
$serviceManager = new ServiceFactoryManager(); // Service factory manager to create various services

// 2. Attachments
$attachmentService     = $serviceManager->createAttachmentCollectionService(); // Attachment service
$attachmentCollection  = $attachmentService->createCollection(); // Attachment collection

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

$blockInterface = new AttachmentList(collection: $attachmentCollection);
echo $blockInterface->renderBlock($templateRenderer->getTwig(), $logger); // Generating and outputting the AttachmentList html

