# Email Generator

![Packagist Version](https://img.shields.io/packagist/v/lemonade/email-generator)
![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue)
![License](https://img.shields.io/badge/license-MIT-green)

Email Generator is a PHP library for generating emails using the [Twig](https://twig.symfony.com/) templating system.
This library allows easy template handling and provides a flexible way to integrate email generation into your project.

## Basic Functionality

Email Generator provides an easy-to-use PHP library that leverages the power of the [Twig templating engine](https://twig.symfony.com/) to generate dynamic emails. The library is highly flexible and allows you to define custom templates for your emails, inject data into those templates, and render them as HTML or plain text emails. Here's how it works:

1. **Template Rendering**: The core feature is the ability to create email templates using Twig. These templates can contain placeholders (variables) that will be replaced by dynamic content at runtime.
2. **Data Injection**: You can pass data (such as user information, order details, etc.) into the templates. The library supports various data types, including arrays, objects, and scalars, allowing you to use your own application logic to populate the email content.
3. **Multi-Language Support**: The library supports translations out of the box, allowing you to generate emails in different languages by swapping dictionaries based on the user's preferred language.
4. **Flexible Email Content**: You can customize the content, including using inline CSS and HTML, embedding dynamic variables, or using more complex email layouts. You can also include graphical attachments using URLs provided via DTO.
5. **Seamless Integration**: It is designed to integrate seamlessly with any PHP project. The library can be used with frameworks like Laravel, Symfony, or as a standalone tool in any PHP application.
6. **Testable**: With the built-in unit tests, you can easily test the behavior of your templates and email generation logic. This ensures that emails are generated correctly under different conditions and with various data inputs.


## Installation

To install the library, use Composer:

```bash
composer require lemonade/email-generator
```

## Usage

Here's a quick example of how to use the library to generate an email:

```php
use Lemonade\EmailGenerator\BlockManager\BlockManager;
use Lemonade\EmailGenerator\Blocks\Component\AttachmentList;
use Lemonade\EmailGenerator\Blocks\Component\ComponentPickupPoint;
use Lemonade\EmailGenerator\Blocks\Component\ComponentNotification;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingAddress;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingFooter;
use Lemonade\EmailGenerator\Blocks\Informational\StaticBlockGreetingHeader;
use Lemonade\EmailGenerator\Blocks\Order\EcommerceCoupon;
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
use Lemonade\EmailGenerator\DTO\CouponData;
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

// Initialize mandatory services
$logger = new FileLogger(config: new FileLoggerConfig(logLevel: LogLevel::WARNING));
$translator = new Translator(currentLanguage: SupportedLanguage::LANG_EN, logger: $logger);
$templateRenderer = new TemplateRenderer(logger: $logger, translator: $translator);
$blockManager = new BlockManager(templateRenderer: $templateRenderer, logger: $logger, translator: $translator);
$serviceManager = new ServiceFactoryManager();
$container = new ContainerBuilder(
    logger: $logger,
    translator: $translator,
    templateRenderer: $templateRenderer,
    blockManager: $blockManager,
    serviceFactoryManager: $serviceManager
);


// Create product collection
$productService = $container->getProductCollectionService();
$productCollection = $productService->createCollection();
$productService->createItem($productCollection, new ProductData(
    productId: 1, productName: "Example Product", productUnitPrice: 1000
));

// Create summary collection
$summaryService = $container->getSummaryCollectionService();
$summaryCollection = $summaryService->createCollection();
$summaryService->createItem($summaryCollection, new SummaryData(name: "Total", value: 1000, final: true));

// Create address
$addressService = $container->getAddressService();
$billingAddress = $addressService->getAddress(new AddressDTO([
    "addressName" => "John Doe",
    "addressStreet" => "123 Street Name",
    "addressCity" => "City",
    "addressCountry" => "Country",
    "addressPostcode" => "12345"
]));

// Add blocks to the email
$blockManager->addBlock(new StaticBlockGreetingHeader());
$blockManager->addBlock(new EcommerceNotify(context: $container->getContextService()->createContext(data: ["webName" => "MY ESHOP SITE"])));
$blockManager->addBlock(new EcommerceHeader(context: $container->getContextService()->createContext([
    "orderId" => "1234567890",
    "orderCode" => "1234567890",
    "orderTotal" => 1000,
    "orderCurrency" => "CZK",
    "orderDate" => date("j.n.Y")
])));
$blockManager->addBlock(new EcommerceProductList(collection: $productCollection, currency: "USD"));
$blockManager->addBlock(new EcommerceSummaryList(collection: $summaryCollection, currency: "USD"));
$blockManager->addBlock(new StaticBlockGreetingFooter());

// Output the email
echo $blockManager->getHtml();

```

---

## Folders and Structure

- `src/` - Contains the main source files of the library:
   - `BlockManager/`
   - `Blocks/`
   - `Collection/`
   - `Context/`
   - `DTO/`
   - `Factories/`
   - `Localization/`
   - `Logger/`
   - `Models/`
   - `Services/`
   - `Template/`
   - `Twig/`
   - `Validators/`
   - `Views/`
   - `ContainerBuilder.php`
- `tests/` - Unit tests for the library, organized by module.
- `demo/` - Contains example implementations for various use cases.

---

## Testing

To run the tests, use the following command:

```bash
./vendor/bin/phpunit vendor/lemonade/email-generator/tests
```

If you have added PHPUnit to your dependencies, the tests will be executed using the `phpunit.xml` configuration.

---


## FAQ

**Q: Can I add attachments to emails?**  
A: Yes, attachments are supported as graphical elements (e.g., images) that are linked dynamically via URLs. These URLs are part of the DTO system and can be customized per email.

**Q: Can I use this library with Laravel?**  
A: Yes, you can integrate it with Laravel using its built-in service container.

---

## Contributing

We welcome contributions to this project! To contribute, please follow these steps:

1. Fork this repository.
2. Create a new branch for your changes.
3. Submit your changes via a pull request.

---

## License

This project is licensed under the MIT License. For more details, see the [LICENSE](./LICENSE) file.

---

## Contact

For more information or to report an issue, visit the [GitHub repository](https://github.com/johnnyxlemonade/email-generator).
