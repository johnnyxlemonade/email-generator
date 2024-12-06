<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\Blocks\Component\ComponentPickupPoint;
use Lemonade\EmailGenerator\Blocks\Component\ComponentNotification;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcomerceAddress;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceDelivery;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceMessage;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceNotify;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceProductList;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceSummaryList;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressDTO;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LogLevel;


require __DIR__ . '/../vendor/autoload.php';

// 1. Vytvoření základních povinných služeb
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::INFO));
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_DA, logger: $logger);
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

// 3. Produkty
$productService    = $container->getProductCollectionService();
$productCollection = $productService->createCollection();

for ($i = 1; $i <= 6; $i++) {
    $productData = new ProductData(
        productId: $i,
        productCode: 'PROD' . $i,
        productName: 'Produkt ' . $i,
        productQuantity: 1,
        productUnitPrice: 1000 + ($i * 100),
        productUnitBase: 900 + ($i * 100),
        productTax: 21,
        productUrl: 'https://google.com/search?q=produkt-' . $i,
        productImage: "https://placehold.co/80x80?font=Roboto&text=Produkt " . $i,
        productData: [
            'color' => [
                'name' => 'Barva',
                'text' => 'Červená'
            ],
            'size' => [
                'name' => 'Velikost',
                'text' => 'M'
            ]
        ],
        useData: ($i%4 == 0)
    );
    $productService->createItem(collection: $productCollection, data: $productData);
}

// 4. Doprava a Platba
$shippingService = $container->getShippingService();
$shipping = $shippingService->createShipping(data: new ShippingData(name: "Doprava kurýrem", price: 150.0));

$paymentService = $container->getPaymentService();
$payment = $paymentService->createPayment(data: new PaymentData(name: "Platba kartou", price: 0.0));

// 5. Výdejní místo
$pickupPointService = $container->getPickupPointService();
$pickupPoint = $pickupPointService->createPickupPoint(data: new PickupPointData(
    id: 'PKP001',
    name: 'Výdejní místo Praha',
    street: 'Ulice 123',
    city: 'Praha',
    openingHours: ['Po - Pá' => '9:00 - 18:00'],
    latitude: 50.0755,
    longitude: 14.4378,
    googleMapKey: ""
));

// 6. Přílohy
$attachmentService    = $container->getAttachmentCollectionService();
$attachmentCollection = $attachmentService->createCollection();
$attachmentData = new AttachmentData(
    name: 'Reklamační řád',
    link: 'https://google.com',
    size: '1MB',
    extension: 'pdf'
);
$attachmentService->createItem(collection: $attachmentCollection, data: $attachmentData);
$attachmentData = new AttachmentData(
    name: 'Obchodní podmínky',
    link: 'https://google.com',
    size: '1MB',
    extension: 'pdf'
);
$attachmentService->createItem(collection: $attachmentCollection, data: $attachmentData);

// 7. Souhrn
$summaryService     = $container->getSummaryCollectionService();
$summaryCollection  = $summaryService->createCollection();

$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Váha", value: "0 g"));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Zboží (s DPH)", value: 3300));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Doprava", value: 150));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Platba", value: 0));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Celkem", value: 3450, final: true));

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

// 9. Přidání bloků do BlockManageru
$blockManager = $container->getBlockManager();
$blockManager->setBlockRenderCenter();
$blockManager->setPageTitle(title: "Rekapitulace objednávky");

// Měna
$currency = "CZK";

// Přidání jednotlivých bloků do emailu
$contextService = $container->getContextService();
$blockManager->addBlock(new StaticBlockGreetingHeader());
$blockManager->addBlock(new EcommerceNotify(context: $contextService->createContext(data: ["webName" => "Můj eshop"])));
$blockManager->addBlock(new ComponentNotification(heading: "Upozornění!", notification: "Bude nutné zkontrolovat dostupnost, váhu a rozměry zboží, aby bylo možné je co nejdříve doručit. \n V případě, že zboží nebude možné zaslat v jedné zásilce, bude nutné kontaktovat zákazníka ohledně rozdělení na více balíků."));
$blockManager->addBlock(new EcommerceHeader(context: $contextService->createContext(data: [
    "orderId" => "1234567890", "orderCode" => "XXX1234567890", "orderTotal" => 666, "orderCurrency" => "Kč", "orderDate" => date(format: "j.n.Y")
])));
$blockManager->addBlock(block: new EcommerceMessage());
$blockManager->addBlock(block: new EcomerceAddress(billingAddress: $billingAddress, deliveryAddress: $deliveryAddress));
$blockManager->addBlock(block: new EcommerceProductList(collection: $productCollection, currency: $currency));
$blockManager->addBlock(block: new EcommerceDelivery(shipping: $shipping, payment: $payment, currency: $currency));
$blockManager->addBlock(block: new ComponentPickupPoint(pickupPoint: $pickupPoint));
$blockManager->addBlock(block: new EcommerceSummaryList(collection: $summaryCollection, currency: $currency));
$blockManager->addBlock(block: new AttachmentList(collection: $attachmentCollection));
$blockManager->addBlock(block: new StaticBlockGreetingFooter());
$blockManager->addBlock(block: new StaticBlockGreetingAddress(address: $footerAddress));

// Výstup HTML emailu
echo $blockManager->getHtml();
