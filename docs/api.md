# API reference

See the [package README](../README.md) for installation and the
[example catalog](../examples/README.md) for executable usage examples. This
page documents contracts, defaults and constraints that are not obvious from a
single example.

## Entry point and immutable documents

Inject `Lemonade\EmailGenerator\EmailGenerator` and call
`create(?string $locale = null, string|Translation $title = '',
string|Translation $preheader = '', Direction $direction = Direction::Ltr)`.
An omitted locale uses the framework localization configuration. The title is
the HTML document title, not a MIME subject; the preheader is hidden inbox
preview text.

`Document` is immutable. Every method that adds content returns a new document;
the original and any sibling branches remain unchanged. Capture the returned
instance when calls are not chained. Components, text values, translations and
stored collections are also immutable snapshots.

### Document methods

| Method | Result |
| --- | --- |
| `add(ComponentInterface ...$components)` | Appends components or expands compositions |
| `heading($content, int $level = 1, HorizontalAlignment $alignment = Left)` | Appends `Heading` |
| `text($content, HorizontalAlignment $alignment = Left)` | Appends `Text` |
| `image(string $url, $alt, int $width = 536, ?int $height = null)` | Appends `Image` |
| `button($label, string $url, ButtonVariant $variant = Primary)` | Appends `Button` |
| `list(array $items, bool $ordered = false)` | Appends `BulletList` |
| `table(array $headers, array $rows, HorizontalAlignment $alignment = Left, array $columnAlignments = [])` | Appends `Table` |
| `divider(Color $color = #e2e8f0, int $thickness = 1)` | Appends `Divider` |
| `spacer(int $height = 16)` | Appends `Spacer` |
| `group(ComponentInterface ...$components)` | Appends a default `Group` |
| `render()` | Returns the complete HTML document |

Typed getters expose `components`, `locale`, `title`, `preheader` and
`direction`. Directly constructed components are appended through `add()`.

## Components

Component classes live in `Lemonade\EmailGenerator\Component`. Constructor
arguments remain available as named arguments and state is exposed through typed
getters. Contact composition data lives in `Lemonade\EmailGenerator\ValueObject`
and is exposed through typed getters.

| Component | Constructor contract and constraints |
| --- | --- |
| `Heading` | `string|Translation|RichText $content`, level 1–6 and optional text alignment |
| `Text` | `string|Translation|RichText $content` and optional text alignment; line breaks are preserved |
| `Image` | Absolute HTTP(S) URL, `string|Translation` alternative text, width 1–2000, optional height 1–2000 |
| `Button` | `string|Translation` label, absolute HTTP(S) URL, `ButtonVariant::Primary` or `Secondary` |
| `BulletList` | List of `string|Translation|RichText`; ordered or unordered |
| `Table` | Rectangular text headers/rows; at least one column, headers or rows required; table-wide and per-column text alignment |
| `Divider` | `Color`, thickness 1–10 pixels |
| `Spacer` | Height 1–1000 pixels |
| `Group` | List of components, optional background/border colors, padding 0–64 pixels |
| `Columns` | Two component lists and gap 0–64 pixels; columns are equal width |
| `Badge` | `string|Translation` label and `BadgeVariant` |
| `ButtonGroup` | At least two `Button` objects, gap 0–64 pixels and `HorizontalAlignment` |
| `Steps` | Non-empty list of `string|Translation` names, process position and visible state labels |
| `KeyValue` | Label and value as `string|Translation|RichText`; logical composition using `Table` |
| `Quote` | Content and optional attribution as `string|Translation|RichText`; logical composition using `Group` and `Text` |
| `Signature` | `Contact $contact`, optional `ImageSource $image` |
| `AddressBlock` | `Address $address` |
| `Hero` | Heading and description as `string|Translation|RichText`, optional `ImageSource` and `Button` |
| `Alert` | Required content, optional heading and `AlertVariant` |
| `SocialLinks` | List of validated `SocialLink` value objects; an empty list emits no content |

`BadgeVariant` provides `Neutral`, `Info`, `Success`, `Warning` and `Danger`.
`HorizontalAlignment` provides `Left`, `Center` and `Right`. It controls text
inside `Text`, `Heading` and `Table`; `Table` column overrides take precedence
over its table-wide setting. For `ButtonGroup`, it controls the placement of the
button group rather than button text. Component-level examples and generated output are linked from the
[example catalog](../examples/README.md#component-examples).

### ButtonGroup

`ButtonGroup` owns the relationship between its buttons. It reuses normal
`Button` rendering without adding the dispatcher's standard rows or 16-pixel
component gaps. Its configured gap is horizontal on desktop and vertical when
the group stacks at viewport widths up to 600 pixels. The MSO fallback remains
horizontal and uses a presentation table.

Primary button markup retains the configured accent background and button text
color. Secondary buttons use the configured content and accent colors with a
visible border. Arbitrary HTML, CSS and per-button styling are not accepted.

### Steps

`currentStep` is one-based. The default `StepsProgress::InProgress` requires a
current position inside the list; earlier steps are completed and later steps
are pending. `StepsProgress::NotStarted` and `StepsProgress::Completed` require
`currentStep: null` and derive all steps as pending or completed respectively.

Completed, current and pending labels default to English strings and each accepts
`string|Translation`. State is expressed through visible text, numbering and
typography in addition to color; the current item uses `aria-current="step"`.
Desktop cells have equal widths and stack at viewport widths up to 600 pixels
where media queries are supported. Rendering fails with `steps.width_invalid`
if nesting leaves fewer than one available pixel per step.

### Contact compositions

`Address` requires name, street, city, postal code and country as
`string|Translation`. It also accepts optional plain-text company ID, tax ID,
email and phone. `Contact` requires a `string|Translation` name and accepts an
optional `string|Translation` position and organization plus optional plain-text
email and phone. `ImageSource` requires an absolute HTTP(S) URL and accepts
`string|Translation` alternative text. All three are `final readonly` value
objects with private state and typed getters; blank optional literal values are
normalized to `null` before a component receives them.

`Signature` renders one compact text block when no image is supplied. With an
image it composes two equal `Columns`, which stack at viewport widths up to 600
pixels where media queries are supported. The image is displayed at a maximum
width of 112 pixels. Blank optional strings are treated as absent and do not
produce empty lines.

`AddressBlock` keeps street, city, postal code and country on separate lines so
it does not impose a country-specific city and postal-code order. Optional
company ID, tax ID, email and phone lines are omitted when null or blank. Their
system labels use the document locale, with built-in Czech and English catalogs
and the configured localization fallback. Applications can override the
`email_generator.address_block.*` keys through their conventional
`app/Language/<locale>/email_generator.php` catalog.

Email addresses and telephone numbers in both compositions are plain text.
`mailto:` and `tel:` links are intentionally unavailable because all links use
the package-wide absolute HTTP(S) URL validation.

```php
use Lemonade\EmailGenerator\Component\{AddressBlock, Signature};
use Lemonade\EmailGenerator\ValueObject\{Address, Contact, ImageSource};

$signature = new Signature(
    contact: new Contact(
        name: 'Jan Novák',
        position: 'Senior Developer',
        organization: 'Lemonade Framework',
        email: 'jan@example.com',
        phone: '+420 123 456 789',
    ),
    image: new ImageSource(
        url: 'https://example.com/avatar.jpg',
        alt: 'Jan Novák',
    ),
);

$addressBlock = new AddressBlock(new Address(
    name: 'Lemonade Framework s.r.o.',
    street: 'Pobřežní 1',
    city: 'Praha',
    postalCode: '186 00',
    country: 'Česká republika',
));
```

### Universal compositions

`Hero` creates one bordered visual group from existing primitives. Its heading
and description are always present; the image and call-to-action are independent
optional values. `ImageSource` supplies the image URL and alternative text, and
the existing responsive `Image` renderer clamps the image to the width remaining
inside the group. The action is a normal `Button`, so it retains standard URL
validation and button variants.

`Alert` accepts `AlertVariant::Info`, `Success`, `Warning` or `Error`. Each variant
uses a distinct fixed background and border from the safe `Color` API. The
optional heading is omitted completely when null. Alert text follows the same
translation, rich-text and escaping rules as `Heading` and `Text`; alerts do not
insert decorative emoji or accept arbitrary icons.

`SocialLinks` has no network allowlist. Each `SocialLink` contains a
`string|Translation` label and an absolute HTTP(S) URL. The composition snapshots
its ordered list and renders normal underlined text links separated by spaces,
allowing email clients to wrap the line on narrow screens. Icon output is not
provided because the public image API does not associate an image with a link.

```php
use Lemonade\EmailGenerator\Component\{Alert, Button, Hero, SocialLinks};
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\ValueObject\{ImageSource, SocialLink};

$hero = new Hero(
    heading: 'Introducing Workspaces',
    description: 'A shared home for plans and decisions.',
    image: new ImageSource('https://example.com/workspaces.png', 'Workspace overview'),
    button: new Button('Explore Workspaces', 'https://example.com/workspaces'),
);

$alert = new Alert(
    content: 'Early access is included with your current plan.',
    heading: 'Available today',
    variant: AlertVariant::Success,
);

$socialLinks = new SocialLinks([
    new SocialLink('Community', 'https://community.example.com/product'),
    new SocialLink('Updates', 'https://updates.example.com/product'),
]);
```

## Formatted and translated content

`RichText` accepts an ordered sequence of literal strings, `Translation` values
and `TextRun` objects. `TextRun` supports `TextStyle::Plain`, `Strong`,
`Emphasis` and `StrongEmphasis`, plus an optional absolute HTTP(S) link. The
`strong()`, `emphasis()` and `link()` named constructors cover common cases.

`Translation` stores a catalog key and scalar/null replacements. Translation is
resolved at render time with the document locale and escaped afterward. Regional
locale names follow the framework catalog convention, such as `pt_BR`; only the
HTML `lang` attribute converts underscores to hyphens. Rendering never changes
the translator's global locale.

## Composition and nesting

`CompositionInterface` is the extension point for reusable logical content. Its
`components()` iterable expands when it is added and does not create a visual
wrapper. `Group`, `Columns` and `ButtonGroup` are preserved visual containers.
Nested content uses the same renderer registry and the width remaining after
parent padding, borders or gaps.

Expansion snapshots mutable compositions, rejects cycles, limits nesting to 32
levels and limits a complete expansion to 10,000 nodes. Unsupported leaf
components are retained during expansion and fail during rendering with
`component.unsupported`; non-component values fail during expansion.

Normal component rows have 16 pixels of bottom spacing. The last visible child
inside a container has no normal trailing gap. `Spacer` supplies explicit
additional spacing. Empty groups and columns with no rendered content emit no
markup; one empty column retains its allocation when its sibling is visible.
Empty lists retain their normal component row.

## Configuration

`EmailConfig` is immutable and combines `EmailWidth`, `EmailPalette` and
`FontFamily`. `EmailPalette` groups the background, content, text, accent and
button label colors while the existing color getters on `EmailConfig` delegate
to it. `EmailWidth` supports `Compact` (480), `Standard` (600, default) and
`Wide` (800) pixels. The outer document reserves 32 pixels of padding on each
side, and nested containers further reduce the available width.

Map `EmailConfigDefinition` to the framework module key `Email` and include
`Email` in the application's shared configuration list. The YAML module is
`email_generator` and accepts:

| Option | Default |
| --- | --- |
| `width` | `600` |
| `background_color` | `f1f5f9` |
| `content_color` | `fff` |
| `text_color` | `1e293b` |
| `accent_color` | `2563eb` |
| `button_text_color` | `fff` |
| `font_family` | `arial` |

Colors use `Color`, which accepts three or six hexadecimal digits with an
optional `#` and normalizes them to lowercase six-digit CSS values. The fluent
`EmailConfigDefinition` exposes matching typed methods for PHP configuration,
including `fontFamily(FontFamily $fontFamily)`. `FontFamily` provides `Arial`,
`Verdana`, `Tahoma`, `Trebuchet`, `Georgia`, `TimesNewRoman` and `CourierNew`;
their configuration values are `arial`, `verdana`, `tahoma`, `trebuchet`,
`georgia`, `times_new_roman` and `courier_new`. Each option emits a complete
fallback stack in inline `font-family` declarations. Unknown options, invalid
types, unsupported widths and unsupported font families are rejected.

## Validation and errors

Dynamic text, translations and attributes are escaped. There is no raw HTML or
arbitrary CSS mode. Text must be valid UTF-8 without disallowed control
characters. Collections must be PHP lists with consecutive numeric indexes.

URLs must be absolute HTTP(S), without credentials, whitespace, control
characters or attribute-injection characters. Relative, `javascript:`, `data:`,
`cid:` and `mailto:` URLs are not supported, and rendering never fetches URLs.

`InvalidInputException` covers invalid public input and extends
`InvalidArgumentException`. `CompositionException` covers composition and
dispatch failures and extends `LogicException`. Both expose a stable `ErrorCode`;
the complete code list is defined in
[`ErrorCode.php`](../src/Exception/ErrorCode.php).

## Rendering boundary

The package generates HTML only. SMTP, MIME messages, plaintext alternatives,
attachments, delivery and queues belong to the host application. Rendering uses
inline CSS, presentation tables, explicit image dimensions and MSO conditional
markup. These fallbacks are covered by automated markup tests, but actual Outlook,
Gmail, dark-mode and image-blocking behavior remains client-dependent and has not
been verified by those tests.
