<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
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
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$updateUrl = 'https://example.com/workspaces/atlas/billing/payment-method';
$invoiceUrl = 'https://example.com/invoices/INV-2027-1018';

return $emails->create(
    locale: 'en',
    title: 'Payment failed for Atlas Workspace',
    preheader: 'Update your payment method by October 21 to keep the workspace active.',
)->add(new Badge('Payment failed', BadgeVariant::Danger))
    ->heading('We could not process your payment')
    ->text('Hi Morgan, the renewal payment for Atlas Workspace was declined. Your workspace remains active while we retry the charge, but the billing details need attention.')
    ->add(new Group(
        children: [
            new Text('AMOUNT DUE'),
            new Heading('$348.00', level: 2),
            new Text(new RichText(
                TextRun::strong('Resolve by October 21, 2027'),
                "\nVisa ending in 4242 was declined",
            )),
        ],
        backgroundColor: new Color('fef2f2'),
        padding: 24,
        borderColor: new Color('fca5a5'),
    ))
    ->add(new Alert(
        content: 'Your card issuer returned “do not honor.” Contact the issuer or use a different payment method before retrying.',
        heading: 'Issuer declined the charge',
        variant: AlertVariant::Error,
    ))
    ->add(KeyValue::listing(
        new KeyValue('Invoice', 'INV-2027-1018'),
        new KeyValue('Workspace', 'Atlas Workspace'),
        new KeyValue('Attempted', 'October 18, 2027 at 08:03 UTC'),
        new KeyValue('Next automatic retry', 'October 20, 2027'),
    ))
    ->add(new ButtonGroup([
        new Button('Update payment method', $updateUrl),
        new Button('View invoice', $invoiceUrl, ButtonVariant::Secondary),
    ]))
    ->heading('What happens next', level: 2)
    ->add(new BulletList([
        'We will retry the payment automatically on October 20.',
        'Workspace members can continue working during the grace period.',
        'If payment remains unresolved after October 21, editing will be paused until billing is restored.',
    ]))
    ->add(new Text(new RichText(
        'Need help with billing? ',
        TextRun::link('Contact the billing team', 'https://example.com/billing/support?invoice=INV-2027-1018'),
        '.',
    )))
    ->render();
