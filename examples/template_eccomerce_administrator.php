<?php


use Lemonade\EmailGenerator\DefaultBuilder;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressData;
use Lemonade\EmailGenerator\DTO\AttachmentData;
use Lemonade\EmailGenerator\DTO\CouponData;
use Lemonade\EmailGenerator\DTO\PaymentData;
use Lemonade\EmailGenerator\DTO\PickupPointData;
use Lemonade\EmailGenerator\DTO\ProductData;
use Lemonade\EmailGenerator\DTO\ShippingData;
use Lemonade\EmailGenerator\DTO\SummaryData;
use Lemonade\EmailGenerator\Localization\SupportedCurrencies;
use Lemonade\EmailGenerator\Localization\SupportedLanguage;


require __DIR__ . '/../vendor/autoload.php';

// 1. Initializing ContainerBuilder with context
$builder  = new DefaultBuilder(container: DefaultContainerBuilder::create(), currency: SupportedCurrencies::EUR);

// 2. Creating products
$productService    = $builder->getContainerBuilder()->getProductCollectionService(); // Product collection service
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
$couponService    = $builder->getContainerBuilder()->getCouponCollectionService();
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
$shippingService = $builder->getContainerBuilder()->getShippingService(); // Shipping service
$shipping = $shippingService->createShipping(data: new ShippingData(name: "Doprava kurýrem", price: 150.0)); // Shipping data

$paymentService = $builder->getContainerBuilder()->getPaymentService(); // Payment service
$payment = $paymentService->createPayment(data: new PaymentData(name: "Platba kartou", price: 0.0)); // Payment data

// 5. Pickup point
$pickupPointService = $builder->getContainerBuilder()->getPickupPointService(); // Pickup point service
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
$attachmentService    = $builder->getContainerBuilder()->getAttachmentCollectionService(); // Attachment service
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
$summaryService     = $builder->getContainerBuilder()->getSummaryCollectionService(); // Summary service
$summaryCollection  = $summaryService->createCollection(); // Summary collection

// Adding summary data
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Váha", value: "0 g"));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Zboží (s DPH)", value: 3300));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Doprava", value: 150));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Platba", value: 0));
$summaryService->createItem(collection: $summaryCollection, data: new SummaryData(name: "Celkem", value: 3450, final: true));

// 8. Addresses
$addressService = $builder->getContainerBuilder()->getAddressService(); // Address service
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

// Using DefaultBuilder to build the email
echo $builder
    ->addHeader(title: "Rekapitulace objednávky")
    ->addAdministratorNotify()
    ->addOrderInfo(orderId: 123456789, orderCode: "1234567890X", orderDate: date(format: "j.n.Y"), statusUrl: "https://google.com")
    ->addCustomerMessage()
    ->addBillingShippingAddress(billing: $billingAddress, shipping: $deliveryAddress)
    ->addProducts(productCollection: $productCollection)
    ->addDelivery(shipping: $shipping, payment: $payment)
    ->addPickup(point: $pickupPoint)
    ->addSummary(summaryCollection: $summaryCollection)
    ->addAttachments(attachmentCollection: $attachmentCollection)
    ->addFooter(footerAddress: $footerAddress)
    ->build();
