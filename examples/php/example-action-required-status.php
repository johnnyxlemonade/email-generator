<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$verificationUrl = 'https://example.com/vendors/VND-1042/verification';
$supportUrl = 'https://example.com/compliance/support?case=VND-1042';

return $emails->create(
    locale: 'en',
    title: 'Action required — complete vendor verification',
    preheader: 'Two documents need attention before your November 15 verification deadline.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR PROCUREMENT'),
    "\nVendor compliance centre",
))->divider(new Color('b91c1c'), thickness: 3)
    ->add(new Badge('Action required', BadgeVariant::Danger))
    ->heading('Your verification is incomplete')
    ->text('Hi Taylor, we reviewed the documents for Redwood Studio Ltd. Two items need attention before the account can be approved for purchase orders and payments.')
    ->add(new Group(
        children: [
            new Text('VERIFICATION DEADLINE'),
            new Heading('November 15, 2027', level: 2),
            new Text(new RichText(
                TextRun::strong('6 days remaining'),
                "\nVendor reference VND-1042",
            )),
        ],
        backgroundColor: new Color('fef2f2'),
        padding: 24,
        borderColor: new Color('f87171'),
    ))
    ->add(new Steps(
        ['Company profile', 'Document review', 'Compliance approval', 'Account active'],
        currentStep: 2,
    ))
    ->heading('Document status', level: 2)
    ->table(
        headers: ['Document', 'Submitted', 'Status'],
        rows: [
            ['Certificate of incorporation', 'Nov 4', new RichText(TextRun::strong('Accepted'))],
            ['Proof of registered address', 'Nov 4', new RichText(TextRun::strong('Replace file'))],
            ['Bank account confirmation', 'Nov 5', new RichText(TextRun::strong('More detail needed'))],
            ['Tax registration', 'Nov 5', new RichText(TextRun::strong('Accepted'))],
        ],
    )
    ->add(new Alert(
        content: 'The address document is older than 90 days. Upload a recent utility bill or bank statement that shows the registered company name and full address.',
        heading: 'Replace proof of address',
        variant: AlertVariant::Error,
    ))
    ->add(new Alert(
        content: 'The confirmation letter shows the account number but not the account holder. Add a statement or bank letter naming Redwood Studio Ltd.',
        heading: 'Complete bank confirmation',
        variant: AlertVariant::Warning,
    ))
    ->heading('Complete the review', level: 2)
    ->add(new BulletList([
        'Open the verification centre and remove the two rejected files.',
        'Upload clear PDF, PNG or JPEG replacements no larger than 10 MB each.',
        'Submit the updated documents for review before November 15.',
    ]))
    ->add(new ButtonGroup([
        new Button('Update verification', $verificationUrl),
        new Button('Contact compliance', $supportUrl, ButtonVariant::Secondary),
    ]))
    ->text('Once resubmitted, reviews usually finish within two business days. Existing account access remains available during verification.')
    ->render();
