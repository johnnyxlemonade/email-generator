# Examples

The examples use the real Lemonade Framework CLI bootstrap and the package's
public API. Generate all versioned previews from the repository root:

```bash
composer examples
```

The command runs `examples/generate.php`. Each file in `php/` returns one email,
and the generator writes the matching filename to `html/`.

| Example | PHP source | HTML preview |
| --- | --- | --- |
| Basic localized email | [`php/basic-email.php`](php/basic-email.php) | [`html/basic-email.html`](html/basic-email.html) |
| Complete order confirmation | [`php/complex-email.php`](php/complex-email.php) | [`html/complex-email.html`](html/complex-email.html) |
| Event registration confirmation | [`php/event-registration.php`](php/event-registration.php) | [`html/event-registration.html`](html/event-registration.html) |
| Monthly framework newsletter | [`php/monthly-newsletter.php`](php/monthly-newsletter.php) | [`html/monthly-newsletter.html`](html/monthly-newsletter.html) |
| New-device security notification | [`php/security-notification.php`](php/security-notification.php) | [`html/security-notification.html`](html/security-notification.html) |
| Payment reminder | [`php/payment-reminder.php`](php/payment-reminder.php) | [`html/payment-reminder.html`](html/payment-reminder.html) |
| Weekly activity report | [`php/weekly-activity-report.php`](php/weekly-activity-report.php) | [`html/weekly-activity-report.html`](html/weekly-activity-report.html) |
| Shipment tracking update | [`php/shipment-tracking.php`](php/shipment-tracking.php) | [`html/shipment-tracking.html`](html/shipment-tracking.html) |
| Support ticket update | [`php/support-ticket-update.php`](php/support-ticket-update.php) | [`html/support-ticket-update.html`](html/support-ticket-update.html) |
| Consultation confirmation | [`php/consultation-confirmation.php`](php/consultation-confirmation.php) | [`html/consultation-confirmation.html`](html/consultation-confirmation.html) |
| Button group and process steps | [`php/button-group-and-steps.php`](php/button-group-and-steps.php) | [`html/button-group-and-steps.html`](html/button-group-and-steps.html) |
| Product announcement | [`php/product-announcement.php`](php/product-announcement.php) | [`html/product-announcement.html`](html/product-announcement.html) |

## Component examples

Every current component and built-in composition has a minimal standalone source
in `php/components/` and a matching generated preview in `html/components/`.

| API | PHP source | HTML preview |
| --- | --- | --- |
| `Heading` | [`heading.php`](php/components/heading.php) | [`heading.html`](html/components/heading.html) |
| `Text` | [`text.php`](php/components/text.php) | [`text.html`](html/components/text.html) |
| `Image` | [`image.php`](php/components/image.php) | [`image.html`](html/components/image.html) |
| `Button` | [`button.php`](php/components/button.php) | [`button.html`](html/components/button.html) |
| `ButtonGroup` | [`button-group.php`](php/components/button-group.php) | [`button-group.html`](html/components/button-group.html) |
| `BulletList` | [`bullet-list.php`](php/components/bullet-list.php) | [`bullet-list.html`](html/components/bullet-list.html) |
| `Table` | [`table.php`](php/components/table.php) | [`table.html`](html/components/table.html) |
| `Divider` | [`divider.php`](php/components/divider.php) | [`divider.html`](html/components/divider.html) |
| `Spacer` | [`spacer.php`](php/components/spacer.php) | [`spacer.html`](html/components/spacer.html) |
| `Group` | [`group.php`](php/components/group.php) | [`group.html`](html/components/group.html) |
| `Columns` | [`columns.php`](php/components/columns.php) | [`columns.html`](html/components/columns.html) |
| `Badge` | [`badge.php`](php/components/badge.php) | [`badge.html`](html/components/badge.html) |
| `Steps` | [`steps.php`](php/components/steps.php) | [`steps.html`](html/components/steps.html) |
| `KeyValue` | [`key-value.php`](php/components/key-value.php) | [`key-value.html`](html/components/key-value.html) |
| `Quote` | [`quote.php`](php/components/quote.php) | [`quote.html`](html/components/quote.html) |
| `Signature` | [`signature.php`](php/components/signature.php) | [`signature.html`](html/components/signature.html) |
| `AddressBlock` | [`address-block.php`](php/components/address-block.php) | [`address-block.html`](html/components/address-block.html) |
| `Hero` | [`hero.php`](php/components/hero.php) | [`hero.html`](html/components/hero.html) |
| `Alert` | [`alert.php`](php/components/alert.php) | [`alert.html`](html/components/alert.html) |
| `SocialLinks` | [`social-links.php`](php/components/social-links.php) | [`social-links.html`](html/components/social-links.html) |
| `RichText` and `TextRun` | [`rich-text.php`](php/components/rich-text.php) | [`rich-text.html`](html/components/rich-text.html) |

`app/` contains the example application configuration, bootstrap and translation
catalogs. `html/` contains selected reproducible previews tracked in the
repository. Versioned previews use public HTTP(S) image URLs and do not depend
on local files. `generate.php` discovers both top-level and component sources,
so a new example only needs a matching PHP filename in the appropriate source
folder.
