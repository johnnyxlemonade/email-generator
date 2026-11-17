<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
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
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$billingUrl = 'https://example.com/workspaces/atlas/billing';
$invoiceUrl = 'https://example.com/invoices/INV-2027-0901';

return $emails->create(
    locale: 'en',
    title: 'Your Team subscription renews on November 1',
    preheader: 'Atlas Workspace will renew for $348.00 using Visa ending in 4242.',
)->add(new Badge('Renewal notice', BadgeVariant::Info))
    ->heading('Your subscription renews soon')
    ->text(new RichText(
        'Atlas Workspace will renew its ',
        TextRun::strong('Team annual subscription'),
        ' on November 1, 2027. Review the details below before the renewal date.',
    ))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Text('NEXT CHARGE'),
                new Heading('$348.00', level: 2),
                new Text('November 1, 2027'),
            ],
            backgroundColor: new Color('eff6ff'),
            padding: 20,
            borderColor: new Color('bfdbfe'),
        )],
        second: [new Group(
            children: [
                new Text('PLAN'),
                new Heading('Team annual', level: 2),
                new Text('12 members · $29.00 per member'),
            ],
            backgroundColor: new Color('f8fafc'),
            padding: 20,
            borderColor: new Color('e2e8f0'),
        )],
        gap: 16,
    ))
    ->heading('Renewal details', level: 2)
    ->add(KeyValue::listing(
        new KeyValue('Workspace', 'Atlas Workspace'),
        new KeyValue('Billing period', 'November 1–30, 2027'),
        new KeyValue('Payment method', 'Visa ending in 4242'),
        new KeyValue('Billing contact', 'finance@atlas.example'),
    ))
    ->table(
        headers: ['Description', 'Quantity', 'Amount'],
        rows: [
            ['Team member seats', '12', '$348.00'],
            ['Included storage', '250 GB', '$0.00'],
            [new RichText(TextRun::strong('Estimated total')), '', new RichText(TextRun::strong('$348.00'))],
        ],
    )
    ->add(new Alert(
        content: 'Seat changes made before October 31 will be reflected automatically in the final invoice.',
        variant: AlertVariant::Info,
    ))
    ->add(new ButtonGroup([
        new Button('Manage billing', $billingUrl),
        new Button('View upcoming invoice', $invoiceUrl, ButtonVariant::Secondary),
    ]))
    ->add(new Text('You are receiving this notice because you are a billing administrator for Atlas Workspace.'))
    ->render();
