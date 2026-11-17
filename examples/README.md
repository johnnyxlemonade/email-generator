# Examples

The examples use the real Lemonade Framework CLI bootstrap and the package's
public API. Generate all local previews from the repository root:

```bash
composer examples
```

To regenerate only one output directory, pass its variant name directly:

```bash
php examples/generate.php desktop
```

Accepted arguments are `compact`, `standard`, `wide` and `desktop`. With no
argument, the script starts one isolated run of itself for every variant. This
keeps example-local helper classes isolated while retaining a single generator
entrypoint.

Each of the 33 files in `php/` remains the single source of its email content.
The generator executes every source with four independently bootstrapped
`EmailGenerator` instances configured through `EmailConfigDefinition`.
All example content and generated documents use English (`lang="en"`).

| Variant | `EmailWidth` | Local output directory |
| --- | --- | --- |
| Compact | `Compact` (480 px) | `html/compact/` |
| Standard | `Standard` (600 px) | `html/standard/` |
| Wide | `Wide` (800 px) | `html/wide/` |
| Desktop | `Desktop` (1200 px) | `html/desktop/` |

The generated `html/` directory is ignored by Git. It is local disposable
output, not a versioned fixture. Running `composer examples` recreates all 132
flat HTML files and removes stale files that no longer have a matching source
and variant.

All four variants use the normal production rendering process. The Desktop
variant has no preview wrapper, CSS width override or HTML post-processing.

## Complete examples

- [`example-basic-email.php`](php/example-basic-email.php)
- [`example-button-group-and-steps.php`](php/example-button-group-and-steps.php)
- [`example-complex-email.php`](php/example-complex-email.php)
- [`example-consultation-confirmation.php`](php/example-consultation-confirmation.php)
- [`example-event-registration.php`](php/example-event-registration.php)
- [`example-monthly-newsletter.php`](php/example-monthly-newsletter.php)
- [`example-payment-reminder.php`](php/example-payment-reminder.php)
- [`example-product-announcement.php`](php/example-product-announcement.php)
- [`example-security-notification.php`](php/example-security-notification.php)
- [`example-shipment-tracking.php`](php/example-shipment-tracking.php)
- [`example-support-ticket-update.php`](php/example-support-ticket-update.php)
- [`example-weekly-activity-report.php`](php/example-weekly-activity-report.php)

## Component examples

Every current component and built-in composition has one minimal
`php/component-*.php` source. The generator writes the matching flat filename
under each of the four local output directories.

`app/` contains the example application configuration, bootstrap and translation
catalogs. The examples use public HTTP(S) image URLs and do not depend on local
assets. `generate.php` discovers all sources in `php/`; the `example-` and
`component-` prefixes identify their category without additional directories.
