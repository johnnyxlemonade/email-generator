<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$invoiceUrl = 'https://example.com/accounting/invoices/INV-2027-1048.pdf';
$deliveryNoteUrl = 'https://example.com/accounting/delivery-notes/DN-2027-8831.pdf';
$statementUrl = 'https://example.com/accounting/statements/2027-10.pdf';
$billingUrl = 'https://example.com/accounting/invoices/INV-2027-1048';

return $emails->create(
    locale: 'en',
    title: 'Invoice INV-2027-1048 from Northstar Supply',
    preheader: 'Invoice INV-2027-1048 for $4,386.00 is due on November 30, 2027.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR SUPPLY'),
    "\nAccounts receivable",
))->divider(new Color('1d4ed8'), thickness: 3)
    ->add(new Badge('Payment due', BadgeVariant::Warning))
    ->heading('Invoice INV-2027-1048')
    ->text('Hello Jordan, please find the billing documents for your October equipment delivery below. The invoice and supporting records are available through secure HTTP links.')
    ->add(new Group(
        children: [
            new Text('AMOUNT DUE'),
            new Heading('$4,386.00', level: 2),
            new Text(new RichText(
                TextRun::strong('Due November 30, 2027'),
                "\nPlease include INV-2027-1048 with your payment.",
            )),
        ],
        backgroundColor: new Color('eff6ff'),
        padding: 24,
        borderColor: new Color('93c5fd'),
    ))
    ->add(KeyValue::listing(
        new KeyValue('Invoice date', 'October 31, 2027'),
        new KeyValue('Customer', 'Harbor Works Ltd.'),
        new KeyValue('Purchase order', 'PO-88419'),
        new KeyValue('Delivery note', 'DN-2027-8831'),
        new KeyValue('Payment terms', '30 days'),
    ))
    ->table(
        headers: ['Description', 'Quantity', 'Amount'],
        rows: [
            ['Environmental sensor kit', '6', '$3,720.00'],
            ['Calibration and commissioning', '1', '$480.00'],
            ['Sales tax', '', '$186.00'],
            [new RichText(TextRun::strong('Total due')), '', new RichText(TextRun::strong('$4,386.00'))],
        ],
    )
    ->add(new AttachmentList([
        new AttachmentItem('Invoice-INV-2027-1048.pdf', 186_000, $invoiceUrl),
        new AttachmentItem('Delivery-note-DN-2027-8831.pdf', 94_000, $deliveryNoteUrl),
        new AttachmentItem('October-account-statement.pdf', 212_000, $statementUrl),
    ]))
    ->add(new Alert(
        content: 'Pay by bank transfer to Northstar Supply Ltd., account ending 4821. Use INV-2027-1048 as the payment reference.',
        heading: 'Payment instructions',
        variant: AlertVariant::Info,
    ))
    ->add(new ButtonGroup([
        new Button('Open billing record', $billingUrl),
        new Button('Contact accounts', 'https://example.com/accounting/support?invoice=INV-2027-1048', ButtonVariant::Secondary),
    ]))
    ->divider()
    ->text('Northstar Supply Ltd. · 41 Market Square, Bristol BS1 4QA · accounts@example.com · +44 117 496 0182')
    ->render();
