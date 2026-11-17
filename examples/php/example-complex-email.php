<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Examples;

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Image;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Composes example-specific care instructions from existing public components
 */
final readonly class OrderCareNote implements CompositionInterface
{
    /**
     * Returns a reusable care section without producing HTML or requiring a renderer
     *
     * @return iterable<ComponentInterface>
     */
    public function components(): iterable
    {
        yield new Heading('Keep your products looking their best', level: 3);
        yield new BulletList([
            'Rinse the ceramics before first use. Subtle variations in the glaze are a natural part of handmade production.',
            'Wash the linen at 40 °C with similar colors. It will become softer and more comfortable with every wash.',
            new RichText('Reuse or recycle the paper filling. ', TextRun::emphasis('Thank you for keeping this in mind.')),
        ]);
    }
}

/** @var EmailGenerator $emails */

$orderNumber = 'DD-2026-10482';
$orderUrl = 'https://example.com/orders/' . $orderNumber;
$border = new Color('e2e8f0');
$softBackground = new Color('f8fafc');

$products = [
    [
        'name' => 'Morning Calm ceramic mug',
        'variant' => 'Ivory · 300 ml',
        'quantity' => 2,
        'unitPrice' => 44900,
        'imageUrl' => 'https://placehold.co/352x352/png?text=Ceramic+Mug',
        'imageWidth' => 176,
        'description' => 'A hand-glazed mug with a comfortable handle. Every piece has subtle variations and its own character.',
    ],
    [
        'name' => 'Everyday linen towel',
        'variant' => 'Sage · 50 × 70 cm',
        'quantity' => 1,
        'unitPrice' => 34900,
        'imageUrl' => 'https://placehold.co/416x416/png?text=Linen+Towel',
        'imageWidth' => 208,
        'description' => 'Natural linen for everyday use. Absorbent, durable and pleasant to the touch, from morning coffee to evening cooking.',
    ],
];
$money = static fn(int $minorUnits): string => number_format($minorUnits / 100, 2, '.', ',') . ' CZK';
$subtotal = 0;
foreach ($products as $product) {
    $subtotal += $product['quantity'] * $product['unitPrice'];
}
$discount = 10000;
$shipping = 7900;
$total = $subtotal - $discount + $shipping;
// All amounts in this fictional order use one illustrative 21% VAT rate.
$taxBase = (int) round($total / 1.21);
$vat = $total - $taxBase;

$document = $emails->create(
    locale: 'en',
    title: new Translation('order.title', ['number' => $orderNumber]),
    preheader: 'Thank you, Klara. We received your payment and are preparing your order. Total ' . $money($total) . '.',
)
    ->image('https://placehold.co/88x88/png?text=GH', 'Good Home house logo', width: 44, height: 44)
    ->text(new RichText(TextRun::strong('GOOD HOME'), "\nHonest essentials for every day"))
    ->divider($border)
    ->spacer(8)
    ->heading(new Translation('order.heading'))
    ->text(new RichText(
        new Translation('order.greeting', ['name' => 'Klara']),
        "\n\n",
        'we received order ',
        TextRun::strong($orderNumber),
        ' successfully. Your payment is confirmed, and we will take care of everything from here.',
    ))
    ->add(new Badge('Paid · preparing', BadgeVariant::Success))
    ->text(new RichText(
        'We plan to dispatch your order on ',
        new TextRun('November 18, 2026', TextStyle::StrongEmphasis),
        '. Once the parcel is handed to the courier, we will send you the tracking number. You can always find the latest information in the ',
        TextRun::link('order details', $orderUrl),
        '.',
    ))
    ->button('View order', $orderUrl)
    ->spacer(16)
    ->heading('Where we will send your parcel', level: 2)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Billing address', level: 3),
                new Text(new RichText(TextRun::strong('Klara Dvorak'), "\nPod Lipami 18\n602 00 Brno\nCzech Republic")),
            ],
            backgroundColor: $softBackground,
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Heading('Delivery address', level: 3),
                new Text(new RichText(TextRun::strong('Klara Dvorak'), "\nLight & Space Studio\nKrizova 24, second floor\n603 00 Brno\nCzech Republic")),
            ],
            backgroundColor: $softBackground,
            padding: 16,
        )],
        gap: 24,
    ))
    ->text(new RichText('Need to update the address? ', TextRun::link('Contact us', 'https://example.com/contact'), ' before we hand the parcel to the courier.'))
    ->spacer(8)
    ->heading('What you selected', level: 2);

foreach ($products as $product) {
    $document = $document->add(new Group(
        children: [new Columns(
            first: [new Image(
                url: $product['imageUrl'],
                alt: $product['name'] . ' — ' . $product['variant'],
                width: $product['imageWidth'],
                height: $product['imageWidth'],
            )],
            second: [
                new Heading($product['name'], level: 3),
                new Text($product['variant']),
                new Text($product['description']),
                new Text(new RichText(
                    TextRun::strong($product['quantity'] . ' pcs'),
                    ' × ' . $money($product['unitPrice']),
                    "\n",
                    TextRun::strong('Total ' . $money($product['quantity'] * $product['unitPrice'])),
                )),
            ],
            gap: 24,
        )],
        padding: 16,
        borderColor: $border,
    ));
}

$document = $document
    ->spacer(8)
    ->heading('Order summary', level: 2)
    ->table(
        headers: ['Item', 'Amount'],
        rows: [
            ['Products · 3 items', $money($subtotal)],
            ['WELCOME discount', '−' . $money($discount)],
            ['Delivery to address', $money($shipping)],
            ['Tax base', $money($taxBase)],
            ['VAT 21%', $money($vat)],
            [new RichText(TextRun::strong('Total including VAT')), new RichText(TextRun::strong($money($total)))],
        ],
    )
    ->text('The tax base and VAT are a breakdown of the total price and are not added again. The full amount has already been paid.')
    ->heading('Payment and delivery', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Ordered', 'November 16, 2026 at 10:42'),
        new KeyValue('Payment', new RichText(TextRun::strong('Online card payment'), "\nPaid · " . $money($total))),
        new KeyValue('Shipping', 'Courier delivery'),
        new KeyValue('Expected delivery', 'November 19–20, 2026'),
        new KeyValue('Shipment status', new RichText(TextRun::link('Track in order details', $orderUrl . '?tab=delivery&source=email'))),
    ))
    ->spacer(12)
    ->add(new Group(
        children: [
            new Heading('What happens next', level: 2),
            new BulletList([
                'We will carefully inspect and securely pack all three items.',
                'After the parcel is handed to the courier, you will receive a separate email with a tracking link.',
                'Your receipt is available in the order details and will remain accessible later.',
            ], ordered: true),
            new Group(
                children: [new Text(new RichText(
                    TextRun::strong('Everything in one parcel.'),
                    ' We protect fragile ceramics with paper filling. If anything still arrives damaged, ',
                    TextRun::link('contact our team', 'https://example.com/contact?topic=delivery'),
                    ' and we will help you find a solution.',
                ))],
                backgroundColor: new Color('ffffff'),
                padding: 12,
            ),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 16,
        borderColor: new Color('bbf7d0'),
    ))
    ->add(new Quote(
        content: new RichText(TextRun::emphasis('Please ring the studio bell. If I am not at the door, wait a moment—I work in the back room. Thank you!')),
        attribution: 'Your delivery note',
    ))
    ->add(new OrderCareNote())
    ->divider($border)
    ->heading('We are here to help', level: 2)
    ->text('Have a question about your order, its materials or product care? Send us your order number and a short description of how we can help.')
    ->button('Contact support', 'https://example.com/contact?order=' . $orderNumber)
    ->add(new Signature(new Contact(
        name: 'Anna Novak',
        position: 'Customer Care',
        organization: 'Good Home',
        email: 'support@example.com',
        phone: '+420 555 010 204',
    )))
    ->text(new RichText(
        TextRun::link('Order details', $orderUrl),
        ' · ',
        TextRun::link('Product care', 'https://example.com/care'),
        ' · ',
        TextRun::link('Returns', 'https://example.com/returns'),
    ))
    ->spacer(24)
    ->divider($border)
    ->add(new Group(
        children: [
            new AddressBlock(new Address(
                name: 'Good Home Ltd.',
                street: 'U Zahrady 12',
                city: 'Brno',
                postalCode: '602 00',
                country: 'Czech Republic',
                email: 'support@example.com',
                phone: '+420 555 010 204',
            )),
            new Text('Customer Care: Monday–Friday, 9:00–16:00'),
            new Text(new RichText(
                'This email concerns order ' . $orderNumber . '.',
                "\n",
                TextRun::link('Terms and conditions', 'https://example.com/terms'),
                ' · ',
                TextRun::link('Privacy policy', 'https://example.com/privacy'),
            )),
            new Text('© 2026 Good Home. This fictional store and example order do not represent a real purchase or tax document.'),
        ],
        backgroundColor: $softBackground,
        padding: 16,
    ))
    ->spacer(32)
    ->divider(new Color('cbd5e1'), thickness: 2)
    ->heading('Demonstration appendix', level: 2)
    ->text('The following section only demonstrates component appearance. These states do not relate to the order above.')
    ->heading('States in deeper nesting', level: 3)
    ->add(new Columns(
        first: [new Group(
            children: [new Columns(
                first: [new Badge('Received', BadgeVariant::Neutral), new Text('New request')],
                second: [new Badge('Information', BadgeVariant::Info), new Text('Customer message')],
                gap: 12,
            )],
            backgroundColor: $softBackground,
            padding: 12,
        )],
        second: [new Group(
            children: [new Columns(
                first: [new Badge('Warning', BadgeVariant::Warning), new Text('Review required')],
                second: [new Badge('Error', BadgeVariant::Danger), new Text('Action required')],
                gap: 12,
            )],
            backgroundColor: $softBackground,
            padding: 12,
        )],
        gap: 16,
    ))
    ->add(new Badge('Completed', BadgeVariant::Success))
    ->heading('Typography and long content', level: 3)
    ->heading('Additional information', level: 4)
    ->text('The quick brown fox jumps over the lazy dog. Curly “quotes,” dashes—and symbols such as ×, %, &, © and € should remain readable when wrapping on a narrow display.')
    ->heading('Formatted link', level: 5)
    ->text(new RichText(
        TextRun::link('Important information & more details', 'https://example.com/help?topic=care&lang=en', TextStyle::StrongEmphasis),
        "\n",
        TextRun::strong('Bold'),
        ' · ',
        TextRun::emphasis('Italic'),
        ' · ',
        new TextRun('Both together', TextStyle::StrongEmphasis),
    ))
    ->heading('Literal text, not HTML', level: 6)
    ->text('The note “<leave behind the gate>” & label A/B are displayed literally. Long variant name: natural sage with a subtle texture and hand-finished edge.')
    ->add(new KeyValue('Reference code', 'DD-2026-10482-PRODUCT-CARE-REQUEST'))
    ->spacer(8);

return $document->render();
