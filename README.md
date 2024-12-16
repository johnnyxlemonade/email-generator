# Email Generator

![License](https://img.shields.io/badge/license-MIT-green)
![Build Status](https://img.shields.io/github/actions/workflow/status/johnnyxlemonade/email-generator/php-ci.yml?branch=master&label=build)
![Packagist Version](https://img.shields.io/packagist/v/lemonade/email-generator)
![PHP Version](https://img.shields.io/badge/php-8.1%20--%208.4-blue)

**Email Generator** is a PHP library for generating emails using the [Twig](https://twig.symfony.com/) templating system.
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

## Usage Example: Lost Password Email

Here’s how to use the DefaultBuilder to create a "lost password" email:


```php
use Lemonade\EmailGenerator\DefaultBuilder;
use Lemonade\EmailGenerator\DefaultContainerBuilder;
use Lemonade\EmailGenerator\DTO\AddressData;

require __DIR__ . '/../vendor/autoload.php';

// Step 1: Create the container with pre-configured services
$container = DefaultContainerBuilder::create();

// Step 2: Create an address for the footer
$addressService = $container->getAddressService(); // Fetch the AddressService from the container
$footerAddress = $addressService->getAddress(new AddressData([
    "addressCompanyId" => "CZ12345678",           // Company ID
    "addressCompanyVatId" => "CZ87654321",       // VAT ID
    "addressCompanyName" => "Firma XYZ",         // Company name
    "addressAlias" => "Sídlo",                   // Address alias
    "addressName" => "Josef Novák",              // Contact person name
    "addressStreet" => "Ulice 1234/56",          // Street address
    "addressPostcode" => "12345",                // Postal code
    "addressCity" => "Praha",                    // City
    "addressCountry" => "CZ",                    // Country code
    "addressPhone" => "+420 123 456 789",        // Contact phone
    "addressEmail" => "info@muj-eshop.com"       // Contact email
]));

// Step 3: Use DefaultBuilder to construct the email
$builder = new DefaultBuilder($container);

// Step 4: Build the email content
echo $builder
    ->addHeader("Password Reset")                           // Add a header with the title "Password Reset"
    ->componentLostPassword("Your Website", "https://example.com/reset-password") // Add a block for password reset with the website name and reset link
    ->addFooter($footerAddress)                             // Add a footer with the previously created address
    ->build();                                              // Build and output the final email HTML

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
