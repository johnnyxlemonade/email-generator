<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\ComponentFormItemList;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\FormItemData;
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
$attachmentService    = $container->getAttachmentCollectionService(); // Attachment service
$attachmentCollection = $attachmentService->createCollection(); // Attachment collection

// 4. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "ComponentFormItemList"); // Set page title

// Form data collection
$formService = $container->getFormItemCollectionService(); // Form item collection service
$formCollection = $formService->createCollection(); // Create a new form item collection

$formData = [ // Sample form data
    ["name" => "Name", "text" => "Franta"],
    ["name" => "Surname", "text" => "Novák"],
    ["name" => "Email", "text" => "franta.novak.12345679@gmail.com"],
    ["name" => "Phone", "text" => "1234567890"],
    ["name" => "Newsletter", "text" => "Yes"],
    ["name" => "Confirmed", "text" => null]
];

foreach($formData as $key => $val) { // Loop through form data and create form items
    $formItem = new FormItemData(name: $val["name"], value: $val["text"]); // Create form item
    $formService->createItem($formCollection, $formItem); // Add form item to collection
}

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(block: new ComponentFormItemList(contextService: $contextService, name: "Contact form", collection: $formCollection)); // Displaying form items in a list

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
