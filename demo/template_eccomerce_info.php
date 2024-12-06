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

// 1. Creating basic mandatory services
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::WARNING)); // Logger service
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_CS, logger: $logger); // Translator service
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

// 3. Creating products
$productService    = $container->getProductCollectionService(); // Product collection service
$productCollection = $productService->createCollection(); // Create product collection

// Adding products to the collection
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
        useData: ($i%4 == 0) // Example flag for conditionally using data
    );
    $productService->createItem(collection: $productCollection, data: $productData); // Adding product item to collection
}

// 4. Shipping and Payment
$shippingService = $container->getShippingService(); // Shipping service
$shipping = $shippingService->createShipping(data: new ShippingData(name: "Doprava kurýrem", price: 150.0)); // Shipping data

$paymentService = $container->getPaymentService(); // Payment service
$payment = $paymentService->createPayment(data: new PaymentData(name: "Platba kartou", price: 0.0)); // Payment data

// 5. Pickup point
$pickupPointService = $container->getPickupPointService(); // Pickup point service
$pickupPoint = $pickupPointService->createPickupPoint(data: new PickupPointData(
    id: 'PKP001',
    name: 'Výdejní místo Praha',
    street: 'Ulice 123',
    city: 'Praha',
    openingHours: ['Po - Pá' => '9:00 - 18:00'],
    latitude: 50.0755,
    longitude: 14.4378,
    googleMapKey: ""
)); // Pickup point data

// 6. Attachments
$attachmentService    = $container->getAttachmentCollectionService(); // Attachment service
$attachmentCollection = $attachmentService->createCollection(); // Attachment collection

// Adding attachment data
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

// 7. Summary
$summaryService     = $container->getSummaryCollectionService(); // Summary service
$summaryCollection  = $summaryService->createCollection(); // Summary collection

// Adding summary data
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Váha", value: "0 g"));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Zboží (s DPH)", value: 3300));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Doprava", value: 150));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Platba", value: 0));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Celkem", value: 3450, final: true));

// 8. Addresses
$addressService = $container->getAddressService(); // Address service
$addressData = new AddressDTO([ // Creating address data
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

// Creating billing and delivery addresses
$billingAddress = $addressService->getAddress(data: $addressData);
$deliveryAddress = $billingAddress->copy();
$footerAddress = $billingAddress->copy();
$footerAddress->setCompanyName(companyName: "MUj ESHOP");
$footerAddress->setAddressEmail(email: "info@muj-eshop.com");

// 9. Adding blocks to BlockManager
$blockManager = $container->getBlockManager(); // Get block manager
$blockManager->setBlockRenderCenter(); // Set block render center
$blockManager->setPageTitle(title: "Rekapitulace objednávky"); // Set page title

// Currency
$currency = "CZK";

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(new StaticBlockGreetingHeader()); // Greeting header block
$blockManager->addBlock(new EcommerceNotify(context: $contextService->createContext(data: ["webName" => "Můj eshop"]))); // E-commerce notification block
$blockManager->addBlock(new ComponentNotification(heading: "Upozornění!", notification: "Bude nutné zkontrolovat dostupnost, váhu a rozměry zboží, aby bylo možné je co nejdříve doručit. \n V případě, že zboží nebude možné zaslat v jedné zásilce, bude nutné kontaktovat zákazníka ohledně rozdělení na více balíků.")); // Notification block
$blockManager->addBlock(new EcommerceHeader(context: $contextService->createContext(data: [
    "orderId" => "1234567890", "orderCode" => "XXX1234567890", "orderTotal" => 666, "orderCurrency" => "Kč", "orderDate" => date(format: "j.n.Y")
]))); // E-commerce header block
$blockManager->addBlock(block: new EcommerceMessage()); // E-commerce message block
$blockManager->addBlock(block: new EcomerceAddress(billingAddress: $billingAddress, deliveryAddress: $deliveryAddress)); // Addresses block
$blockManager->addBlock(block: new EcommerceProductList(collection: $productCollection, currency: $currency)); // Products list block
$blockManager->addBlock(block: new EcommerceDelivery(shipping: $shipping, payment: $payment, currency: $currency)); // Delivery and payment block
$blockManager->addBlock(block: new ComponentPickupPoint(pickupPoint: $pickupPoint)); // Pickup point block
$blockManager->addBlock(block: new EcommerceSummaryList(collection: $summaryCollection, currency: $currency)); // Summary list block
$blockManager->addBlock(block: new AttachmentList(collection: $attachmentCollection)); // Attachments list block
$blockManager->addBlock(block: new StaticBlockGreetingFooter()); // Footer greeting block
$blockManager->addBlock(block: new StaticBlockGreetingAddress(address: $footerAddress)); // Footer address block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email
