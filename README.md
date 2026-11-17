# Lemonade Email Generator

[![PHPStan](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpstan.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpstan.yml)
[![Tests](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpunit.yml)
[![Coding Standards](https://github.com/johnnyxlemonade/email-generator/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/coding-standards.yml)
[![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](LICENSE)

`johnnyxlemonade/email-generator` provides component-based HTML email generation
for Lemonade Framework applications. It integrates with the framework container,
configuration and localization services and supports both HTTP and CLI use.

Applications compose immutable documents from typed components, formatted text
and translations. The package renders HTML with table-based layouts, inline CSS,
responsive columns and Outlook fallback markup.

## Installation

The package requires PHP `>=8.3 <8.6` and
`johnnyxlemonade/framework:dev-main`.

```bash
composer require johnnyxlemonade/email-generator:dev-main
```

Append the provider to your existing `app/Config/Providers.yaml`:

```yaml
module: providers
config:
  providers:
    - Lemonade\EmailGenerator\EmailServiceProvider
```

Include `Providers` in the `shared` section of `app/Config/Config.yaml` to make
the service available to both HTTP and CLI entrypoints:

```yaml
shared:
  - Providers
```

The standard framework bootstrap registers localization. Custom framework
provider lists must retain `LocalizationServiceProvider`; the email provider
uses its existing translator and default locale configuration.

## Usage

Inject `Lemonade\EmailGenerator\EmailGenerator` into an application service or
command and use it to create a document:

```php
$html = $emails
    ->create(locale: 'en', title: 'Welcome', preheader: 'Your account is ready.')
    ->heading('Welcome')
    ->text('Thank you for signing up.')
    ->button('Activate account', 'https://example.com/activate')
    ->render();
```

Every fluent method returns a new document. Retain its return value when calling
methods separately. `title` sets the HTML document title, not a MIME subject.

Built-in components include `Heading`, `Text`, `Image`, `Button`, `ButtonGroup`,
`Steps`, `BulletList`, `Table`, `Divider`, `Spacer`, `Group`, `Columns`, `Badge`,
`KeyValue`, `Quote`, `Signature`, `AddressBlock` and `AttachmentList`.
Use `RichText` and `TextRun` for inline formatting, `Translation` for framework
catalog entries, and `CompositionInterface` for reusable groups of components
without renderer registration. `Signature` accepts a `Contact` and optional
`ImageSource`; `AddressBlock` accepts an `Address`; `AttachmentList` accepts
validated `AttachmentItem` metadata. These immutable value objects
own their data validation and normalization.

See the [API reference](docs/api.md) for component options,
nesting, immutable documents, configuration, localization and validation.

## Package boundaries

- Lemonade Framework owns the container, application bootstrap, configuration and translation services.
- Email Generator owns document composition, content validation and HTML rendering.
- The host application owns business content, public image URLs, MIME messages, delivery, physical attachments and queues.

The public API accepts typed content and options. Text and attributes are
escaped; URLs must be absolute HTTP(S). Raw HTML, arbitrary CSS and custom
renderer configuration are not part of the application API.

Generated markup includes Outlook fallbacks, but appearance depends on the
email client. Automated tests verify markup and fixtures; they do not verify
rendering inside Outlook or Gmail.

## Examples

[generate.php](examples/generate.php) builds local previews from the matching
readable sources in [`examples/php/`](examples/php/). They cover complete email
scenarios plus minimal standalone examples of every current component and
built-in composition.

```bash
composer examples
```

The examples include versioned PHP sources, application configuration and
translation catalogs. Generated HTML is written to the ignored `examples/html/`
directory. See the
[example README](examples/README.md) for the directory layout, catalog and layout
notes.

## Development and QA

From the package root, install dependencies and run:

```bash
composer install
composer qa
```

`composer qa` runs PHP CS Fixer, PHPStan and PHPUnit. GitHub Actions cover
[coding standards](.github/workflows/coding-standards.yml),
[static analysis](.github/workflows/phpstan.yml) and
[tests on PHP 8.3–8.5](.github/workflows/phpunit.yml).

## License

[Apache License 2.0](LICENSE).
