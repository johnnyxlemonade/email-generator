# Lemonade Email Generator

[![PHPStan](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpstan.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpstan.yml)
[![Tests](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpunit.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/phpunit.yml)
[![Coding Standards](https://github.com/johnnyxlemonade/email-generator/actions/workflows/coding-standards.yml/badge.svg)](https://github.com/johnnyxlemonade/email-generator/actions/workflows/coding-standards.yml)
[![License](https://img.shields.io/badge/license-Apache--2.0-blue.svg)](LICENSE)

Lemonade Email Generator builds HTML emails from typed, reusable content components. It integrates with Lemonade Framework's dependency injection, configuration and localization services, and works in both HTTP applications and CLI commands.

```php
$html = $emails
    ->create(locale: 'en')
    ->heading('Welcome')
    ->text('Thank you for signing up.')
    ->button('Activate account', $url)
    ->render();
```

## Features

- Fluent API, logical compositions and visual group containers.
- Safe inline formatting with strong text, emphasis and links.
- Heading, text, image, button, list, table, divider, spacer, group, columns and badge components.
- Key-value pairs and highlighted quotes composed from existing components.
- Immutable content objects and email documents.
- Framework translations with an explicit locale for each document.
- Typed configuration with `EmailWidth`, `Direction` and `Color` values.
- Escaped text and attributes, validated URLs and structured exception codes.
- Table-based layout, inline CSS and Outlook-specific markup.
- Pure PHP rendering without a template engine or HTTP request dependency.

## Requirements

- PHP 8.3, 8.4 or 8.5 (`>=8.3 <8.6`).
- `johnnyxlemonade/framework:dev-main`.

## Installation

Install the package with Composer:

```bash
composer require johnnyxlemonade/email-generator:dev-main
```

Append the provider to your existing providers in `app/Config/Providers.yaml`:

```yaml
module: providers
config:
  providers:
    - Lemonade\EmailGenerator\EmailServiceProvider
```

Include `Providers` in the `shared` section of `app/Config/Config.yaml` to make the service available to both HTTP and CLI entrypoints:

```yaml
shared:
  - Providers
```

The standard framework bootstrap already registers localization. Applications with a custom framework provider list must retain `LocalizationServiceProvider`. The email provider uses its existing `TranslatorInterface` and `LocalizationConfig` services.

## Usage

Inject `EmailGenerator` into an application service or command:

```php
use Lemonade\EmailGenerator\EmailGenerator;

/**
 * Builds the account activation email using the shared email generator
 */
final readonly class WelcomeEmail
{
    /**
     * Receives the application email generator
     */
    public function __construct(private EmailGenerator $emails) {}

    /**
     * Renders an English welcome email with the supplied activation link
     */
    public function build(string $url): string
    {
        return $this->emails->create(locale: 'en')
            ->heading('Welcome')
            ->text('Thank you for signing up.')
            ->button('Activate account', $url)
            ->render();
    }
}
```

Each call to `create()` returns a new immutable `Document`. Every fluent method returns a new document and leaves the original unchanged. Chaining works as before; when calling a fluent method separately, always capture its return value. Branches can share a common base without cloning:

```php
$base = $emails->create(locale: 'en')->heading('Welcome');
$registration = $base->text('Thank you for signing up.');
$reminder = $base->text('Please activate your account.');

$registration = $registration->button('Activate account', $url);
$html = $registration->render();
// $base still contains only the heading; $reminder is unchanged.
```

Built-in content components are immutable. Components and content value objects keep their properties private and expose typed getters, such as `$image->url()`, `$image->width()`, `$group->children()` and `$richText->runs()`. Constructor arguments remain available as named arguments.

### Document options

```php
use Lemonade\EmailGenerator\Enum\Direction;

$email = $emails->create(
    locale: 'en',
    title: 'Welcome to the application',
    preheader: 'Your account is ready.',
    direction: Direction::Ltr,
);
```

`title` sets the HTML document title, not a MIME subject. `preheader` supplies hidden preview text. Omitted locale uses the framework's configured default locale, independent of the current request or translator override. Use `Direction::Rtl` for right-to-left content.

### Components

All component classes belong to `Lemonade\EmailGenerator\Component`.

| Fluent method | Component | Purpose |
| --- | --- | --- |
| `heading($content, level: 1)` | `Heading` | Heading level 1–6 |
| `text($content)` | `Text` | Paragraph with preserved line breaks |
| `image($url, $alt, width: 536, height: null)` | `Image` | Image with alternative text |
| `button($label, $url)` | `Button` | Link presented as a button |
| `list($items, ordered: false)` | `BulletList` | Unordered or ordered text list |
| `table($headers, $rows)` | `Table` | Rectangular data table |
| `divider($color, thickness: 1)` | `Divider` | Horizontal separator; default color `e2e8f0` |
| `spacer(height: 16)` | `Spacer` | Additional vertical spacing |
| `group(...$components)` | `Group` | Visual container with default spacing |
| `add(new Columns($first, $second, gap: 16))` | `Columns` | Two responsive columns with nested components |
| `add(new KeyValue($label, $value))` | `KeyValue` | Label/value pair composed as a table |
| `add(new Badge($label, $variant))` | `Badge` | Compact label with a predefined status palette |
| `add(new Quote($content, attribution: null))` | `Quote` | Highlighted text composed as a group |
| `add(...$components)` | — | Append components or custom compositions |
| `render()` | — | Render the complete HTML document |

`List` is a PHP keyword, so the corresponding class is named `BulletList`. Headings, paragraphs, list items and table cells accept `string|Translation|RichText`. Button labels, image alternative text, document titles and preheaders remain `string|Translation`. List items, table headers and table rows must be PHP lists with consecutive indexes starting at zero. Table cells contain text values, not HTML or nested components. A table may omit headers or contain headers without rows, but must define at least one column. Empty lists and groups are allowed.

Image dimensions must be between 1 and 2000 pixels. The renderer limits image width to the document's inner width and adjusts an explicit height proportionally. Alternative text is required; use an empty string for decorative images. Spacer height is 1–1000 pixels and divider thickness is 1–10 pixels. Content rows have 16 pixels of bottom spacing; the last child inside a group has no additional bottom spacing. A spacer adds explicit spacing.

## Composition and visual groups

`Contract\CompositionInterface` represents a logical composition: it expands into components without adding a visual wrapper. `Component\Group` is a visual container: it stays in the document tree and renders as its own presentation table.

### Visual groups

```php
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;

$section = new Group(
    children: [
        new Heading('Next steps', level: 2),
        new Text('Activate your account and complete your profile.'),
    ],
    backgroundColor: new Color('f0fdf4'),
    padding: 24,
    borderColor: new Color('15803d'),
);

$html = $emails->create(locale: 'en')->add($section)->render();
```

The `Group` constructor takes a list of components. Groups may contain other groups and custom compositions. The optional background and one-pixel border surround all children together. Padding defaults to 16 pixels and accepts 0–64 pixels. Images respect the available width after every enclosing group's padding and borders. If nesting leaves no room for content, rendering raises `group.width_invalid`.

The fluent shorthand `$email->group($heading, $text)` creates a group with default options. Use `add(new Group(...))` for explicit styling. Empty groups render no markup. Groups preserve vertical order and do not introduce columns or a general-purpose CSS layout system.

### Columns, key-value pairs, badges and quotes

```php
use Lemonade\EmailGenerator\Component\{Badge, Columns, Heading, KeyValue, Quote};
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;

$html = $emails->create(locale: 'en')->add(new Columns(
    first: [
        new Heading('Account', level: 2),
        new Badge('Active', BadgeVariant::Success),
        KeyValue::listing(
            new KeyValue('Name', 'Alex'),
            new KeyValue('Next step', new Translation('emails.activate')),
        ),
    ],
    second: [
        new Quote(
            new RichText('Thank you for ', TextRun::strong('joining us'), '.'),
            attribution: 'The team',
        ),
        new KeyValue('Plan', 'Team'),
    ],
    gap: 24,
))->render();
```

`Columns` accepts two lists of arbitrary built-in components or custom compositions. Both lists are detached from external array references; custom compositions are snapshotted when added to a document. Groups and columns can nest in either direction. The default gap is 16 pixels, with a range of 0–64. The renderer subtracts the gap from the available parent width, then divides the remainder equally; an odd remaining pixel goes to the second column. Nested groups subtract their own padding and borders. Widths that leave less than one pixel per column raise `columns.width_invalid`.

Columns use inline-block fluid layout and an MSO conditional table with explicit pixel widths for classic Outlook. At viewport widths up to 600 pixels, a component-local media rule stacks the columns at full available width and turns the gutter into vertical spacing. Without media-query support, the inline blocks can wrap when their combined desktop widths no longer fit. Images retain their desktop column maximum dimensions to preserve Outlook sizing. Inherited text direction applies to desktop column order; mobile content follows first, then second. Empty columns retain their allocated slots when the other column has content; if both render no rows, the entire component is omitted. The last child of each column has no normal trailing gap; explicit spacers remain intact.

`KeyValue` accepts `string|Translation|RichText` for both label and value. A single pair expands into a two-cell `Table`; `KeyValue::listing($first, ...$rest)` returns one existing `Table` with aligned rows and requires at least one pair. It deliberately reuses the table's borders, spacing, escaping and inline formatting rather than introducing another data-table renderer.

`Badge` accepts a plain or translated label and a `BadgeVariant`: `Neutral` (default), `Info`, `Success`, `Warning` or `Danger`. Each variant has fixed foreground and background colors; applications cannot inject CSS or HTML. A small presentation table keeps the label compact and preserves its padding in Outlook. Labels should be short and name the status so meaning does not depend on color alone.

`Quote` accepts `string|Translation|RichText` content and an optional attribution of the same types. It expands into `Text` paragraphs in a `Group` with 16-pixel padding, a light background and a one-pixel border. This provides a consistent visual quotation or highlight without a new HTML primitive; it does not emit a semantic `<blockquote>`. Use typed emphasis in `RichText` when desired.

Only `Columns` and `Badge` require new HTML renderers: existing components cannot express responsive child columns or a compact colored status label. `KeyValue` and `Quote` implement `CompositionInterface` and reuse existing rendering. All four are available through `Document::add()` with no application renderer setup.

### Logical compositions

Custom components implement `CompositionInterface` and return built-in components or other compositions. They require no registration and do not produce HTML:

```php
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\CompositionInterface;

/**
 * Composes a personalized welcome heading and introductory paragraph
 */
final readonly class IntroSection implements CompositionInterface
{
    /**
     * Sets the recipient name used in the welcome heading
     */
    public function __construct(private string $name) {}

    /**
     * Returns the welcome heading followed by the introductory paragraph
     *
     * @return iterable<Heading|Text>
     */
    public function components(): iterable
    {
        yield new Heading('Welcome, ' . $this->name);
        yield new Text('Thank you for joining us.');
    }
}

$html = $emails->create()->add(new IntroSection('Alex'))->render();
```

A custom composition can yield a `Group` when its content needs a shared visual container. A plain composition adds no container on its own. All components implement `Contract\ComponentInterface`; `CompositionInterface` extends that contract.

Compositions expand when added to a document, including compositions nested inside groups and columns. Container boundaries and options are preserved. Later changes to a custom mutable composition cannot affect content already added. Addition is atomic: a failed expansion leaves the document unchanged. Cycles, nesting beyond 32 levels and expansions exceeding 10,000 nodes raise exceptions. These limits apply to the whole expansion, including groups and columns.

The expander understands only the `Group` and `Columns` containers and `CompositionInterface`. It preserves other `ComponentInterface` objects as leaf components without consulting a list of supported types. The renderer's existing dispatch map is the sole authority for rendering support. Applications still implement `CompositionInterface` to reuse built-in components; accepting a leaf during expansion does not register a renderer for it.

**Validation timing change:** an unsupported `ComponentInterface` previously raised `InvalidInputException` with `component.unsupported` during `Document::add()` or construction. It is now retained in the document and raises `CompositionException` with the same code during `Document::render()`, including inside groups, either column and custom compositions. Rendering throws instead of returning HTML with the unknown component omitted. Values that do not implement `ComponentInterface`, such as a string yielded by a malformed composition, still raise `InvalidInputException` with `component.unsupported` during expansion. Cycle, depth and size errors also remain expansion-time errors.

## Formatted text

Use immutable `RichText` and `TextRun` values to combine literal text, translations and formatting:

```php
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\Enum\TextStyle;

$html = $emails->create(locale: 'en')->text(new RichText(
    'Hello ',
    TextRun::strong('Alex'),
    ', please ',
    TextRun::emphasis('review'),
    ' ',
    TextRun::link('your account', 'https://example.com/account', TextStyle::Strong),
    '.',
))->render();
```

`RichText` accepts a sequence of `string`, `Translation` and `TextRun` values. Each run contains `string|Translation`, an optional HTTP(S) URL, and one of `TextStyle::Plain`, `Strong`, `Emphasis` or `StrongEmphasis`. The `strong()`, `emphasis()` and `link()` methods are convenient constructors; `new TextRun($content, style: TextStyle::StrongEmphasis)` combines both styles.

The same content works in headings, list items, table headers and table cells. Text and translation replacements are always escaped, including styled text and link labels. Line breaks are preserved. URLs use the same validation as buttons and images. Runs cannot contain other runs, components or HTML nodes, so nested links and arbitrary markup cannot be introduced through this API.

```php
use Lemonade\EmailGenerator\ValueObject\Translation;

$greeting = new RichText(
    TextRun::strong(new Translation('emails.welcome', ['name' => 'Alex'])),
);

$html = $emails->create(locale: 'en')->heading($greeting)->render();
```

## Localization

Strings are literal text. Use `Translation` to reference framework translation keys:

```php
use Lemonade\EmailGenerator\ValueObject\Translation;

$html = $emails->create(locale: 'en')
    ->heading(new Translation('emails.welcome', ['name' => 'Alex']))
    ->button(new Translation('emails.activate'), $url)
    ->render();
```

Create `app/Language/en/emails.php` using the framework's standard catalog format:

```php
<?php

return [
    'welcome' => 'Welcome, {name}!',
    'activate' => 'Activate account',
];
```

Translations also work in image alternative text, list items, table cells, document titles and preheaders. The renderer passes the document locale explicitly to `TranslatorInterface::get()` and never calls `setLocale()`. Catalog lookup, fallback and missing-key behavior remain the framework's responsibility. A missing key without a fallback is rendered as the key itself.

Regional locales must match their catalog names, such as `pt_BR`. Underscores are converted to hyphens only for the HTML `lang` attribute. Translation results and replacement values are escaped after resolution. Translations resolve at render time.

## Configuration

`EmailConfig` is immutable, with private properties and typed getters. Width uses the `EmailWidth` enum, and colors use the `Color` value object:

```php
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\Enum\EmailWidth;

$config = new EmailConfig(
    width: EmailWidth::Standard,
    backgroundColor: new Color('f1f5f9'),
    accentColor: new Color('15803d'),
);

$config->width();                  // EmailWidth::Standard
$config->backgroundColor()->hex(); // '#f1f5f9'
(new Color('AbC'))->hex();         // '#aabbcc'
```

Available widths are `Compact` (480 pixels), `Standard` (600 pixels, the default) and `Wide` (800 pixels). `Color` accepts three or six hexadecimal digits with an optional `#` prefix and normalizes them to lowercase six-digit CSS hex. Arbitrary CSS expressions are rejected.

### Application configuration

Add the definition mapping to your existing `app/Config/ConfigMap.php`:

```php
<?php

use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;

return ['Email' => EmailConfigDefinition::class];
```

Create `app/Config/Email.yaml` and include `Email` in the manifest's `shared` section:

```yaml
module: email_generator
config:
  width: 600
  background_color: 'f1f5f9'
  content_color: 'fff'
  text_color: '1e293b'
  accent_color: '2563eb'
  button_text_color: 'fff'
```

These are the default values; individual options may be omitted. YAML width must be the integer 480, 600 or 800. Later configuration definitions override earlier values. Unknown keys, invalid types, invalid colors and unsupported widths raise exceptions.

The framework also supports a PHP file returning a typed definition. Create `app/Config/Email.php` and list `Email.php` in the manifest instead:

```php
<?php

use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\Configuration\EmailConfigDefinition;
use Lemonade\EmailGenerator\Enum\EmailWidth;

return EmailConfigDefinition::create()
    ->width(EmailWidth::Wide)
    ->accentColor(new Color('15803d'));
```

## Validation and exceptions

All dynamic text and HTML attributes are escaped. HTML supplied as content is displayed literally; there is no raw HTML mode. Input text must be valid UTF-8 without control characters other than tabs and line breaks.

URLs must be absolute HTTP(S), without credentials, whitespace, control characters or attribute-injection characters. Relative URLs and `javascript:`, `data:`, `cid:` and `mailto:` schemes are not supported. The renderer does not fetch URLs.

Exceptions live in the `Exception` namespace. `InvalidInputException` extends PHP's `InvalidArgumentException`; `CompositionException` extends `LogicException`. Their `errorCode()` method returns an `ErrorCode` enum with a stable string value:

```php
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;

try {
    $color = new Color('invalid');
} catch (InvalidInputException $exception) {
    if ($exception->errorCode() === ErrorCode::ColorInvalid) {
        // $exception->errorCode()->value === 'color.invalid'
    }
}
```

Codes include `color.invalid`, `url.invalid`, `width.invalid`, `group.children_invalid`, `group.width_invalid`, `config.unknown_option` and `composition.cycle`. See [ErrorCode.php](src/Exception/ErrorCode.php) for the complete list. Native PHP type errors and exceptions from custom compositions or framework translation services propagate unchanged.

## Rendering and compatibility

The shared renderer uses presentation tables, inline CSS, explicit image dimensions, an MSO conditional width wrapper and 96 DPI settings for Outlook. Data tables retain semantic column headers. System fonts, fluid widths and table-based buttons require no JavaScript or external stylesheets.

The markup is designed for common email clients, including Outlook. Actual appearance remains client-dependent: classic Outlook may ignore rounded corners, while dark mode, image blocking and inbox previews are controlled by the client. Automated tests cover the generated markup, not rendering in actual email clients.

Component renderers and their explicit registration are internal implementation details. Applications continue to inject `EmailGenerator` and compose immutable documents without configuring renderers. Groups and columns render their children through the same dispatcher, with group padding and borders or the column gap subtracted from the available width.

The package generates HTML only. It does not implement SMTP, MIME messages, attachments, plaintext alternatives, delivery or queues.

## Package structure

- `src/Component`: immutable primitives, visual containers and built-in compositions.
- `src/Contract`: `ComponentInterface` and `CompositionInterface`.
- `src/Document.php`, `src/EmailGenerator.php`: document assembly and the injectable entry point.
- `src/Presentation`: document rendering, the component dispatcher, shared row layout and render context.
- `src/Presentation/Renderer`: one internal HTML renderer per primitive, using `Presentation/Contract/HtmlComponentRendererInterface`, plus shared inline formatting in `RichTextRenderer`.
- `src/Composition`: `ComponentExpander`, which snapshots logical compositions and retains groups and columns.
- `src/Validation`: `InputValidator` for shared input validation.
- `src/Text`: immutable `RichText` and `TextRun` content.
- `src/Configuration`: typed configuration, framework definitions and resolver.
- `src/Enum`: `EmailWidth`, `Direction`, `TextStyle` and `BadgeVariant`.
- `src/ValueObject`: validated `Color` and `Translation` values.
- `src/Exception`: exceptions and stable error codes.
- `tests`: component, composition, escaping, validation, localization and framework integration tests.
- `examples`: executable PHP examples with application configuration and translation catalogs.

## Examples

The example application uses the framework's CLI kernel and the package provider. Generate the example files with:

```bash
composer examples
```

Only example source code, configuration and translation catalogs are versioned. Generated HTML in `examples/output/` and local preview images in `examples/assets/` are ignored by Git. Both generators create the output directory when needed. Regression baselines in `tests/Fixtures/html/` remain versioned.

[generate.php](examples/generate.php) produces `welcome-cs.html`, `welcome-en.html`, `components.html`, `universal-components.html` and `rtl.html`. The separate [complex-email.php](examples/complex-email.php) example produces `complex-email.html`.

The component example uses a remote demonstration PNG with alternative text for clients that block images.

### Complete order confirmation

[complex-email.php](examples/complex-email.php) builds a realistic Czech order confirmation for the fictional shop Dobrý domov using only the public email service, components and logical composition contract. It includes all 13 components: `Heading`, `Text`, `Image`, `Button`, `BulletList`, `Table`, `Divider`, `Spacer`, `Group`, `Columns`, `Badge`, `KeyValue` and `Quote`. `OrderCareNote` is an example-local `CompositionInterface` implementation that yields existing components; it has no renderer or registration.

The main message includes billing and delivery addresses, two product lines (three units), a calculated price breakdown, payment and delivery details, customer notes and a support footer. Prices are calculated in integer minor units; the illustrative 21% VAT is extracted from the final total, not added twice. Translated title and greeting use [the Czech example catalog](examples/app/Language/cs/order.php). All company details, products, dates, contact information and destination links are demonstration data.

A clearly separated appendix exercises all five badge variants, `Columns` inside `Group` inside `Columns`, heading levels 3–6, long text, literal HTML-looking strings, special characters and combined strong/emphasis links. The main message already uses heading levels 1–3, ordered and unordered lists, different image widths, nested groups and 8/12/16/24/32-pixel spacers. Regular component rows retain their existing 16-pixel gap in addition to explicit spacers.

Generate this example separately so the other example HTML files remain untouched:

```bash
php examples/complex-email.php
```

In this workspace, use the existing DDEV PHP runtime:

```bash
ddev exec -d /var/www/html/email-generator php examples/complex-email.php
```

When generated inside this DDEV workspace, the example automatically uses `DDEV_PRIMARY_URL` for the image URLs. Open [the DDEV preview](https://lemonade-development.ddev.site/email-generator/examples/output/complex-email.html), or open the generated HTML file directly while DDEV is running. No separate asset server is needed in DDEV. Images are local preview resources and are not included in the repository. To reproduce the illustrated preview after a fresh checkout, provide `logo.png`, `ceramic-mug.png` and `linen-towel.png` in `examples/assets/complex-email/`, or supply an HTTP(S) asset base hosting files with those names. Without these resources the HTML still generates, but images show their alternative text.

`Image` accepts only absolute HTTP(S) URLs, so relative files, `file:`, `data:` and `cid:` cannot be used through the current public API. Outside DDEV, the default asset base is `http://127.0.0.1:8765/assets/complex-email`. For that fallback, serve the examples directory from the repository root:

```bash
python3 -m http.server 8765 --bind 127.0.0.1 --directory examples
```

Then open [the standalone local preview](http://127.0.0.1:8765/output/complex-email.html). Stop the server with Ctrl+C when finished. To choose a different asset host explicitly, pass its base URL as the first argument:

```bash
ddev exec -d /var/www/html/email-generator php examples/complex-email.php https://lemonade-development.ddev.site/email-generator/examples/assets/complex-email
```

An actual sent email needs publicly accessible HTTPS image URLs; DDEV and localhost addresses are for local previews only. The generated HTML is never rewritten to bypass URL validation.

Current layout limits exposed by this example:

- Columns are equal-width only and stack at viewport widths up to 600 pixels. Product image/detail proportions cannot be configured independently. Images retain their desktop maximum width after stacking.
- Tables and key-value pairs use equal-width, bordered cells with fixed padding. There is no public numeric alignment, column width or totals-row styling option; totals use `RichText` emphasis. The two-column summary avoids squeezing a wide product table onto a phone.
- Typography, button appearance and normal row spacing are renderer-defined. The public API does not provide small muted footer text, a secondary button style, or per-row spacing controls. Groups and explicit spacers provide the available visual hierarchy.
- Links accept HTTP(S) only, so the contact email and phone number are plain text. Mail and telephone links would need a deliberate API enhancement.
- The example uses a fixed appearance for `Quote`; it cannot request a semantic blockquote or custom quote colors. No internal rendering API or raw HTML is used to work around these constraints.

Browser inspection is left to the user. Check widths 320, 375, 600 and 800 pixels, especially column stacking, long table cells, product images and the nested appendix. No Outlook or Gmail rendering test was performed; the presence of MSO fallback markup and passing fixture tests is not evidence of actual inbox appearance.

## Development

Install development dependencies and run the standard package QA command:

```bash
composer install
composer qa
```

`composer qa` runs PHP CS Fixer, PHPStan and PHPUnit, following the conventions used by the other Lemonade packages. PHPStan uses level 8 with strict rules; PHPUnit uses the 10.5 series, and CS Fixer uses the shared PER-CS2.0 rules. The [PHPUnit workflow](.github/workflows/phpunit.yml) defines a PHP 8.3–8.5 matrix. Separate workflows cover [PHPStan](.github/workflows/phpstan.yml) and [coding standards](.github/workflows/coding-standards.yml). These source files live in the hidden `.github/workflows` directory; include that directory when preparing source archives.
