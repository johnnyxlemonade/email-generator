<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\ComponentLostPassword;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressData;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LogLevel;

require __DIR__ . '/../vendor/autoload.php';

// 1. Creating basic mandatory services
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::WARNING)); // Logger service for error level logs
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_CS, logger: $logger); // Translator service for Czech language
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

// 3. Addresses
$addressService = $container->getAddressService(); // Address service to handle address data
$addressData = new AddressData([ // Address data DTO for customer details
    "addressCompanyId" => "CZ12345678", // Company ID
    "addressCompanyVatId" => "CZ87654321", // Company VAT ID
    "addressCompanyName" => "Firma XYZ", // Company name
    "addressAlias" => "Sídlo", // Alias for address
    "addressName" => "Josef Novák", // Customer name
    "addressStreet" => "Ulice 1234/56", // Street address
    "addressPostcode" => "12345", // Postal code
    "addressCity" => "Praha", // City
    "addressCountry" => "CZ", // Country
    "addressPhone" => "123456789", // Customer phone
    "addressEmail" => "josef.novak@example.com" // Customer email
]);

$billingAddress = $addressService->getAddress(data: $addressData); // Retrieve billing address
$deliveryAddress = $billingAddress->copy(); // Copy the billing address to delivery address
$footerAddress = $billingAddress->copy(); // Copy the billing address to footer address
$footerAddress->setCompanyName(companyName: "MUj ESHOP"); // Set company name for footer address
$footerAddress->setAddressEmail(email: "info@muj-eshop.com"); // Set company email for footer address

// 4. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center for content rendering
$blockManager->setPageTitle(title: "Požadavek na změnu hesla"); // Set the page title for the email

// 5. Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service to create context data
$blockManager->addBlock(block: new StaticBlockGreetingHeader()); // Greeting header block
$blockManager->addBlock(block: new ComponentLostPassword(webName: "Můj eshop", anchorLink: "https://google.com/search?q=lost-password&identity=123456")); // Lost password component with link
$blockManager->addBlock(block: new StaticBlockGreetingFooter()); // Footer greeting block
$blockManager->addBlock(block: new StaticBlockGreetingAddress(address: $footerAddress)); // Footer address block

// Output HTML email
echo $blockManager->getHtml(); // Generate and output the HTML email

