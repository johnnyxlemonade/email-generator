# Examples

The example application uses the framework's CLI kernel and the package provider. Generate the example files with:

```bash
composer examples
```

Only example source code, configuration and translation catalogs are versioned. Generated HTML in `examples/output/` and local preview images in `examples/assets/` are ignored by Git. Both generators create the output directory when needed. Regression baselines in `tests/Fixtures/html/` remain versioned.

[generate.php](../examples/generate.php) produces `welcome-cs.html`, `welcome-en.html`, `components.html`, `universal-components.html` and `rtl.html`. The separate [complex-email.php](../examples/complex-email.php) example produces `complex-email.html`.

The component example uses a remote demonstration PNG with alternative text for clients that block images.

## Complete order confirmation

[complex-email.php](../examples/complex-email.php) builds a realistic Czech order confirmation for the fictional shop Dobrý domov using only the public email service, components and logical composition contract. It includes all 13 components: `Heading`, `Text`, `Image`, `Button`, `BulletList`, `Table`, `Divider`, `Spacer`, `Group`, `Columns`, `Badge`, `KeyValue` and `Quote`. `OrderCareNote` is an example-local `CompositionInterface` implementation that yields existing components; it has no renderer or registration.

The main message includes billing and delivery addresses, two product lines (three units), a calculated price breakdown, payment and delivery details, customer notes and a support footer. Prices are calculated in integer minor units; the illustrative 21% VAT is extracted from the final total, not added twice. Translated title and greeting use [the Czech example catalog](../examples/app/Language/cs/order.php). All company details, products, dates, contact information and destination links are demonstration data.

A clearly separated appendix exercises all five badge variants, `Columns` inside `Group` inside `Columns`, heading levels 3–6, long text, literal HTML-looking strings, special characters and combined strong/emphasis links. The main message already uses heading levels 1–3, ordered and unordered lists, different image widths, nested groups and 8/12/16/24/32-pixel spacers. Regular component rows retain their existing 16-pixel gap in addition to explicit spacers.

Generate this example separately so the other example HTML files remain untouched:

```bash
php examples/complex-email.php
```

Images are not included in the repository. Provide `logo.png`, `ceramic-mug.png`
and `linen-towel.png` in `examples/assets/complex-email/`, or pass an HTTP(S)
asset base hosting files with those names. Without these resources the HTML
still generates, but images show their alternative text.

When run inside DDEV, the example uses `DDEV_PRIMARY_URL` with the path
`/email-generator/examples/assets/complex-email` by default. For other project
layouts, pass the asset base explicitly.

`Image` accepts only absolute HTTP(S) URLs, so relative files, `file:`, `data:` and `cid:` cannot be used through the current public API. Outside DDEV, the default asset base is `http://127.0.0.1:8765/assets/complex-email`. For that fallback, serve the examples directory from the repository root:

```bash
python3 -m http.server 8765 --bind 127.0.0.1 --directory examples
```

Then open [the standalone local preview](http://127.0.0.1:8765/output/complex-email.html). Stop the server with Ctrl+C when finished. To choose a different asset host explicitly, pass its base URL as the first argument:

```bash
php examples/complex-email.php https://example.com/email-assets
```

An actual sent email needs publicly accessible HTTPS image URLs; DDEV and localhost addresses are for local previews only. The generated HTML is never rewritten to bypass URL validation.

Current layout limits exposed by this example:

- Columns are equal-width only and stack at viewport widths up to 600 pixels. Product image/detail proportions cannot be configured independently. Images retain their desktop maximum width after stacking.
- Tables and key-value pairs use equal-width, bordered cells with fixed padding. There is no public numeric alignment, column width or totals-row styling option; totals use `RichText` emphasis. The two-column summary avoids squeezing a wide product table onto a phone.
- Typography, button appearance and normal row spacing are renderer-defined. The public API does not provide small muted footer text, a secondary button style, or per-row spacing controls. Groups and explicit spacers provide the available visual hierarchy.
- Links accept HTTP(S) only, so the contact email and phone number are plain text. Mail and telephone links would need a deliberate API enhancement.
- The example uses a fixed appearance for `Quote`; it cannot request a semantic blockquote or custom quote colors. No internal rendering API or raw HTML is used to work around these constraints.

For a manual preview, check widths 320, 375, 600 and 800 pixels, especially column stacking, long table cells, product images and the nested appendix. No Outlook or Gmail rendering test was performed; the presence of MSO fallback markup and passing fixture tests is not evidence of actual inbox appearance.

