<?php

use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\Blocks\Component\ComponentPickupPoint;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceNotifyCustomer;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceStatusButton;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceCoupon;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceAddress;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceDelivery;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceMessage;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceProductList;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceSummaryList;
use Lemonade\EmailGenerator\ContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressData;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\DTO\CouponData;
use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Factories\ServiceFactoryManager;
use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;
use Lemonade\EmailGenerator\Localization\Translator;
use Lemonade\EmailGenerator\Logger\FileLogger;
use Lemonade\EmailGenerator\Logger\FileLoggerConfig;
use Lemonade\EmailGenerator\Template\TemplateRenderer;
use Psr\Log\LogLevel;

require __DIR__ . '/../vendor/autoload.php';

// 1. Creating basic mandatory services
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::DEBUG)); // Logger service
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

// 3. Creating products
$productService    = $container->getProductCollectionService(); // Product collection service
$productCollection = $productService->createCollection(); // Create product collection

// Adding products to the collection
for ($i = 1; $i <= 6; $i++) {
    $productData = new ProductData(
        productId: $i,
        productName: 'Produkt ' . $i,
        productCode: 'PROD' . $i,
        productQuantity: 1,
        productUnitPrice: 1000 + ($i * 100),
        productUnitBase: 900 + ($i * 100),
        productTax: 21,
        productUrl: 'https://google.com/search?q=produkt-' . $i,
        productImage: ($i === 3 ? "https://placehold.co/80x80/black/white?font=Roboto&text=Produkt " . $i : null), // Example flag for image thumbnail
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
        useData: ($i%4 === 0) // Example flag for conditionally using data
    );
    $productService->createItem(collection: $productCollection, data: $productData); // Adding product item to collection
}

// 3.1 Creating coupon
$couponService    = $container->getCouponCollectionService();
$couponCollection = $couponService->createCollection();

// Adding coupont to the collection
for ($i = 1; $i <= 3; $i++) {

    $couponData = new CouponData(
        name: "Coupon " . $i,
        code: "COUPON-CODE-".$i,
        price: 100
    );

    $couponService->createItem($couponCollection, $couponData);
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
$addressData = new AddressData([ // Creating address data
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
$blockManager->setCurrency(currency: SupportedCurrencies::EUR);
$blockManager->setPageTitle(title: "Rekapitulace objednávky"); // Set page title

// Adding individual blocks to the email
$contextService = $container->getContextService(); // Context service
$blockManager->addBlock(block: new StaticBlockGreetingHeader()); // Greeting header block
$blockManager->addBlock(block: new EcommerceNotifyCustomer()); // E-commerce notification block
$blockManager->addBlock(block: new EcommerceHeader(contextService: $container->getContextService(), orderId: 123456789, orderCode: "1234567890X", orderDate: date("j.n.Y"))); // E-commerce header block
$blockManager->addBlock(block: new EcommerceStatusButton(url: "https://google.com"));
$blockManager->addBlock(block: new EcommerceAddress(contextService: $container->getContextService(), billingAddress: $billingAddress, deliveryAddress: $deliveryAddress)); // Addresses block
$blockManager->addBlock(block: new EcommerceProductList(contextService: $container->getContextService(), collection: $productCollection)); // Products list block
$blockManager->addBlock(block: new EcommerceMessage(contextService: $contextService, message: "Dsads"));
$blockManager->addBlock(block: new EcommerceDelivery(contextService: $container->getContextService(), shipping: $shipping, payment: $payment)); // Delivery and payment block
$blockManager->addBlock(block: new ComponentPickupPoint(contextService: $container->getContextService(), pickupPoint: $pickupPoint)); // Pickup point block
$blockManager->addBlock(block: new EcommerceCoupon(contextService: $container->getContextService(), collection: $couponCollection));
$blockManager->addBlock(block: new EcommerceSummaryList(contextService: $container->getContextService(), collection: $summaryCollection)); // Summary list block
$blockManager->addBlock(block: new AttachmentList(contextService: $container->getContextService(), collection: $attachmentCollection)); // Attachments list block
$blockManager->addBlock(block: new StaticBlockGreetingFooter()); // Footer greeting block
$blockManager->addBlock(block: new StaticBlockGreetingAddress(contextService: $container->getContextService(), address: $footerAddress)); // Footer address block

// Output HTML email
echo $blockManager->getHtml(); // Generating and outputting the HTML email