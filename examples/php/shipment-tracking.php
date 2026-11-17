<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$trackingUrl = 'https://example.com/shipments/TRK-8472-1195';
$deliveryUrl = 'https://example.com/shipments/TRK-8472-1195/preferences';
$supportUrl = 'https://example.com/delivery/support?shipment=TRK-8472-1195';
$blueBackground = new Color('eff6ff');
$blueBorder = new Color('93c5fd');

return $emails->create(
    locale: 'en',
    title: 'Your shipment is out for delivery',
    preheader: 'Shipment TRK-8472-1195 is expected today between 13:00 and 16:00.',
)->text(new RichText(
    TextRun::strong('NORTHLINE DELIVERY'),
    "\nShipment update",
))->divider(new Color('2563eb'), thickness: 3)
    ->add(new Badge('Out for delivery', BadgeVariant::Info))
    ->heading('Your parcel arrives today')
    ->text('Good news, Jordan. Your shipment has reached the local delivery depot and is now with the courier for its final journey.')
    ->add(new Group(
        children: [
            new Text('ESTIMATED DELIVERY'),
            new Heading('Today, 13:00–16:00'),
            new Text(new RichText(
                TextRun::strong('Courier route 42'),
                "\nWe will update the tracking page if the delivery window changes.",
            )),
        ],
        backgroundColor: $blueBackground,
        padding: 24,
        borderColor: $blueBorder,
    ))
    ->add(new Steps(
        ['Shipment confirmed', 'In transit', 'Out for delivery', 'Delivered'],
        currentStep: 3,
    ))
    ->heading('Shipment details', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Tracking number', new RichText(TextRun::strong('TRK-8472-1195'))),
        new KeyValue('Carrier', 'Northline Delivery'),
        new KeyValue('Service', 'Priority parcel'),
        new KeyValue('Order reference', 'ND-2027-008421'),
        new KeyValue('Package', '1 parcel · 2.4 kg'),
    ))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Delivery address', level: 3),
                new Text(new RichText(
                    TextRun::strong('Jordan Lee'),
                    "\nStudio 5, Harbor Works",
                    "\n18 Riverside Road",
                    "\nBristol BS1 4QA",
                )),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Heading('Delivery instructions', level: 3),
                new Text("Ring reception for Studio 5. If no one answers, leave the parcel with the staffed front desk and record the recipient’s name."),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
        )],
        gap: 16,
    ))
    ->heading('Latest tracking activity', level: 2)
    ->table(
        headers: ['Time', 'Update'],
        rows: [
            ['08:14', new RichText(TextRun::strong('Out for delivery'), "\nBristol local depot")],
            ['06:42', new RichText(TextRun::strong('Sorted at delivery depot'), "\nBristol local depot")],
            ['01:18', new RichText(TextRun::strong('Arrived at regional hub'), "\nSouth West distribution centre")],
        ],
    )
    ->add(new ButtonGroup(
        buttons: [
            new Button('Track shipment', $trackingUrl),
            new Button('Delivery preferences', $deliveryUrl, ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: ButtonGroupAlignment::Center,
    ))
    ->add(new Group(
        children: [new Text(new RichText(
            TextRun::strong('Keep your delivery code private.'),
            ' Northline will never ask for payment or account credentials before handing over this prepaid parcel.',
        ))],
        backgroundColor: new Color('f0fdf4'),
        padding: 16,
        borderColor: new Color('bbf7d0'),
    ))
    ->divider(new Color('cbd5e1'))
    ->text(new RichText(
        'Questions about delivery? ',
        TextRun::link('Contact shipment support', $supportUrl),
        "\nNorthline Delivery · This is a fictional shipment notification.",
    ))
    ->render();
