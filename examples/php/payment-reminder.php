<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$paymentUrl = 'https://example.com/billing/invoices/INV-2027-0412/pay';
$invoiceUrl = 'https://example.com/billing/invoices/INV-2027-0412';
$billingSupportUrl = 'https://example.com/billing/support?invoice=INV-2027-0412';
$warmBackground = new Color('fff7ed');
$warmBorder = new Color('fdba74');

return $emails->create(
    locale: 'en',
    title: 'Payment reminder for invoice INV-2027-0412',
    preheader: 'Invoice INV-2027-0412 for €2,480.00 is overdue.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR STUDIO'),
    "\nBilling and account services",
))->divider(new Color('ea580c'), thickness: 3)
    ->add(new Badge('Payment overdue', BadgeVariant::Warning))
    ->heading('A payment is still outstanding')
    ->text('Hello Morgan, our records show that the invoice below has passed its due date and remains unpaid. Please arrange payment or contact us if anything needs to be corrected.')
    ->add(new Group(
        children: [
            new Text('TOTAL AMOUNT DUE'),
            new Heading('€2,480.00'),
            new Text(new RichText(
                TextRun::strong('Due May 31, 2027'),
                "\n12 days overdue",
            )),
        ],
        backgroundColor: $warmBackground,
        padding: 24,
        borderColor: $warmBorder,
    ))
    ->heading('Invoice summary', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Invoice number', 'INV-2027-0412'),
        new KeyValue('Issue date', 'May 1, 2027'),
        new KeyValue('Due date', new RichText(TextRun::strong('May 31, 2027'))),
        new KeyValue('Payment status', 'Overdue · unpaid'),
        new KeyValue('Customer', 'Morgan Hale · Bright Path Labs'),
    ))
    ->table(
        headers: ['Description', 'Amount'],
        rows: [
            ['Product strategy workshop', '€1,800.00'],
            ['Research synthesis package', '€600.00'],
            ['VAT adjustment', '€80.00'],
            [new RichText(TextRun::strong('Total due')), new RichText(TextRun::strong('€2,480.00'))],
        ],
    )
    ->heading('Bank transfer details', level: 2)
    ->add(new Group(
        children: [KeyValue::listing(
            new KeyValue('Account name', 'Northstar Studio s.r.o.'),
            new KeyValue('IBAN', 'CZ65 0800 0000 0019 2000 1453'),
            new KeyValue('BIC / SWIFT', 'GIBACZPX'),
            new KeyValue('Variable symbol', new RichText(TextRun::strong('20270412'))),
            new KeyValue('Payment reference', 'INV-2027-0412'),
        )],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Pay invoice securely', $paymentUrl),
            new Button('View invoice', $invoiceUrl, ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: ButtonGroupAlignment::Left,
    ))
    ->add(new Group(
        children: [new Text(new RichText(
            TextRun::strong('Already paid?'),
            ' Thank you. Bank transfers can take up to two business days to appear. You may ignore this reminder if the payment is already in progress.',
        ))],
        backgroundColor: new Color('fefce8'),
        padding: 16,
        borderColor: new Color('fde047'),
    ))
    ->divider(new Color('fed7aa'))
    ->text(new RichText(
        'Questions about this invoice? ',
        TextRun::link('Contact billing support', $billingSupportUrl),
        "\nNorthstar Studio · billing@example.com · +420 555 017 240",
        "\nThis is a fictional payment reminder for demonstration purposes.",
    ))
    ->render();
