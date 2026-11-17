<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$receiptUrl = 'https://example.com/orders/NS-84721/receipt';

return $emails->create(
    locale: 'en',
    title: 'Receipt for order NS-84721',
    preheader: 'Payment received for your Northstar Supply order totaling $186.40.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR SUPPLY'),
    "\nReceipt · Order NS-84721",
))->divider(new Color('0f766e'), thickness: 3)
    ->add(new Badge('Paid', BadgeVariant::Success))
    ->heading('Thanks for your order, Taylor')
    ->text('Your payment was approved and this email is your receipt. We will send tracking information separately when the parcel leaves our warehouse.')
    ->add(new Group(
        children: [KeyValue::listing(
            new KeyValue('Order number', 'NS-84721'),
            new KeyValue('Purchase date', 'October 6, 2027 at 11:24 PDT'),
            new KeyValue('Payment', 'Visa ending in 4242'),
            new KeyValue('Transaction', 'txn_demo_84721'),
        )],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->heading('Items', level: 2)
    ->table(
        headers: ['Item', 'Qty', 'Price'],
        rows: [
            ['Canvas field bag · Olive', '1', '$118.00'],
            ['Brass key clip', '2', '$34.00'],
            ['Ground shipping', '1', '$12.00'],
            ['Sales tax', '', '$22.40'],
            [new RichText(TextRun::strong('Total paid')), '', new RichText(TextRun::strong('$186.40'))],
        ],
        columnAlignments: [
            1 => HorizontalAlignment::Center,
            2 => HorizontalAlignment::Right,
        ],
    )
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Ship to', level: 3),
                new AddressBlock(new Address(
                    name: 'Taylor Brooks',
                    street: '418 Cedar Avenue',
                    city: 'Portland, OR',
                    postalCode: '97205',
                    country: 'United States',
                )),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
        )],
        second: [new Group(
            children: [
                new Heading('Delivery estimate', level: 3),
                new Text(new RichText(
                    TextRun::strong('October 10–12'),
                    "\nGround shipping",
                    "\nTracking follows after dispatch",
                )),
            ],
            backgroundColor: new Color('f0fdfa'),
            padding: 16,
        )],
        gap: 16,
    ))
    ->button('View printable receipt', $receiptUrl)
    ->text('Northstar Supply · This fictional receipt is for demonstration purposes and is not a tax document.')
    ->render();
