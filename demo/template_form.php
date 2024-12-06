<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\ComponentBlockText;
use Lemonade\EmailGenerator\Blocks\Component\ComponentFormItemList;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressDTO;
use Lemonade\EmailGenerator\DTO\FormItemData;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LogLevel;

require __DIR__ . '/../vendor/autoload.php';

// 1. Vytvoření základních povinných služeb
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::ERROR));
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_CS, logger: $logger);
$templateRenderer = new TemplateRenderer(logger: $logger, translator: $translator);
$blockManager = new BlockManager(templateRenderer: $templateRenderer, logger: $logger, translator: $translator);
$serviceManager = new ServiceFactoryManager();

// 2. Inicializace ContainerBuilder s kontextem
$container = new ContainerBuilder(
    logger: $logger,
    translator: $translator,
    templateRenderer: $templateRenderer,
    blockManager: $blockManager,
    serviceFactoryManager: $serviceManager
);

// 8. Adresy
$addressService = $container->getAddressService();
$addressData = new AddressDTO([
    "addressCompanyId" => "CZ12345678",
    "addressCompanyVatId" => "CZ87654321",
    "addressCompanyName" => "Firma XYZ",
    "addressAlias" => "Sídlo",
    "addressName" => "Josef Novák",
    "addressStreet" => "Ulice 1234/56",
    "addressPostcode" => "12345",
    "addressCity" => "Praha",
    "addressCountry" => "CZ",
    "addressPhone" => "123456789",
    "addressEmail" => "josef.novak@example.com"
]);


$billingAddress = $addressService->getAddress(data: $addressData);
$deliveryAddress = $billingAddress->copy();
$footerAddress = $billingAddress->copy();
$footerAddress->setCompanyName(companyName: "MUj ESHOP");
$footerAddress->setAddressEmail(email: "info@muj-eshop.com");

// Formular kolekce
$formService    = $container->getFormItemCollectionService();
$formCollection = $formService->createCollection();

$formData = [
    ["name" => "Celé jméno", "text" => "Franta Pepík"],
    ["name" => "Email", "text" => "franta.pepik.12345679@gmail.com"],
    ["name" => "Telefon", "text" => null]
];

foreach($formData as $key => $val) {

    $formItem = new FormItemData(name: $val["name"], value: $val["text"]);

    $formService->createItem($formCollection, $formItem);
}


// Přidání bloků do BlockManageru
$blockManager = $container->getBlockManager();
$blockManager->setBlockRenderCenter();
$blockManager->setPageTitle(title: "Formulářová data");


// Přidání jednotlivých bloků do emailu
$contextService = $container->getContextService();
$blockManager->addBlock(block: new StaticBlockGreetingHeader());
$blockManager->addBlock(block: new ComponentBlockText(message: "z Vašich webových stránek jsme obdrželi dotaz prostřednictvím formuláře. Veškerá vyplněná data z formuláře zasíláme níže jako součást této zprávy."));
$blockManager->addBlock(block: new ComponentFormItemList(name: "Kontaktní formulář", collection: $formCollection));
$blockManager->addBlock(block: new StaticBlockGreetingFooter());
$blockManager->addBlock(block: new StaticBlockGreetingAddress(address: $footerAddress));

// Výstup HTML emailu
echo $blockManager->getHtml();
