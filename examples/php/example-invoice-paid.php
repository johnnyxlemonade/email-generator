<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\AddressBlock;
use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Address;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$invoiceUrl = 'https://example.com/billing/invoices/INV-2027-2198';
$receiptUrl = 'https://example.com/billing/receipts/RCPT-771042.pdf';

return $emails->create(
    locale: 'en',
    title: 'Payment received for invoice INV-2027-2198',
    preheader: 'We received your bank transfer of $12,480.00 and the invoice balance is now zero.',
)->text(new RichText(
    TextRun::strong('MERIDIAN SYSTEMS'),
    "\nAccounts receivable",
))->divider(new Color('166534'), thickness: 4)
    ->add(new Badge('Paid in full', BadgeVariant::Success))
    ->heading('Payment received', alignment: HorizontalAlignment::Center)
    ->text('Thank you. Your payment has been matched to invoice INV-2027-2198 and posted to the Northstar Logistics account.', HorizontalAlignment::Center)
    ->add(new Group(
        children: [
            new Text('AMOUNT PAID', HorizontalAlignment::Center),
            new Heading('$12,480.00', level: 2, alignment: HorizontalAlignment::Center),
            new Text(new RichText(
                TextRun::strong('Balance due: $0.00'),
                "\nReceived November 16, 2027",
            ), HorizontalAlignment::Center),
        ],
        backgroundColor: new Color('f0fdf4'),
        padding: 28,
        borderColor: new Color('4ade80'),
    ))
    ->heading('Payment details', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Invoice number', new RichText(TextRun::strong('INV-2027-2198'))),
        new KeyValue('Payment date', 'November 16, 2027'),
        new KeyValue('Payment method', 'Bank transfer · account ending 7038'),
        new KeyValue('Payment reference', 'NLS-INV2198-161127'),
        new KeyValue('Receipt number', 'RCPT-771042'),
    ))
    ->table(
        headers: ['Allocation', 'Amount'],
        rows: [
            ['Invoice total', '$12,480.00'],
            ['Payment applied', '−$12,480.00'],
            [new RichText(TextRun::strong('Remaining balance')), new RichText(TextRun::strong('$0.00'))],
        ],
        columnAlignments: [1 => HorizontalAlignment::Right],
    )
    ->heading('Billing parties', level: 2)
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Supplier', level: 3),
                new AddressBlock(new Address(
                    name: 'Meridian Systems Ltd.',
                    street: '18 Wellington Quay',
                    city: 'Dublin',
                    postalCode: 'D02 K138',
                    country: 'Ireland',
                    companyId: '638214',
                    taxId: 'IE638214A',
                    email: 'accounts@example.com',
                )),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
            borderColor: new Color('e2e8f0'),
        )],
        second: [new Group(
            children: [
                new Heading('Customer', level: 3),
                new AddressBlock(new Address(
                    name: 'Northstar Logistics GmbH',
                    street: 'Hafenstrasse 42',
                    city: 'Hamburg',
                    postalCode: '20457',
                    country: 'Germany',
                    companyId: 'HRB 184920',
                    taxId: 'DE349184202',
                    email: 'finance@example.com',
                )),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 16,
            borderColor: new Color('e2e8f0'),
        )],
        gap: 16,
    ))
    ->heading('Payment document', level: 2)
    ->add(new AttachmentList([
        new AttachmentItem('Payment-receipt-RCPT-771042.pdf', 184_000, $receiptUrl),
    ]))
    ->add(new Alert(
        content: 'No further payment is required for this invoice. The receipt records the payment allocation; your original invoice remains the accounting and tax document.',
        heading: 'Account is settled',
        variant: AlertVariant::Success,
    ))
    ->button('View paid invoice', $invoiceUrl)
    ->divider()
    ->text("Meridian Systems Accounts Receivable\naccounts@example.com · +353 1 555 0142 · Monday–Friday, 09:00–17:00 GMT")
    ->render();
