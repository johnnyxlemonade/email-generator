<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Signature;
use Lemonade\EmailGenerator\Component\Steps;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Contact;

/** @var EmailGenerator $emails */

$signatureUrl = 'https://example.com/signing/envelopes/ENV-584291';
$reviewUrl = 'https://example.com/signing/envelopes/ENV-584291/documents';

return $emails->create(
    locale: 'en',
    title: 'Signature requested — Master Services Agreement',
    preheader: 'Elena Martin has asked you to sign the agreement by November 24, 2027.',
)->text(new RichText(
    TextRun::strong('NORTHSTAR SIGN'),
    "\nSecure electronic signature",
))->divider(new Color('0f172a'), thickness: 4)
    ->add(new Badge('Signature requested', BadgeVariant::Info))
    ->heading('Master Services Agreement')
    ->text('Hello Daniel, Elena Martin from Northstar Engineering has sent a contract package for your electronic signature. Review all documents before completing the signature fields.')
    ->add(new Group(
        children: [
            new Text('DOCUMENT TYPE'),
            new Heading('Commercial services agreement', level: 2),
            new Text(new RichText(
                TextRun::strong('Envelope ENV-584291'),
                "\nVersion 4 · prepared November 16, 2027",
            )),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 24,
        borderColor: new Color('94a3b8'),
    ))
    ->add(new Columns(
        first: [
            new Heading('Sent by', level: 3),
            new Text(new RichText(
                TextRun::strong('Elena Martin'),
                "\nNorthstar Engineering",
                "\nelena.martin@example.com",
            )),
        ],
        second: [
            new Heading('Requested from', level: 3),
            new Text(new RichText(
                TextRun::strong('Daniel Foster'),
                "\nFoster Analytics Ltd.",
                "\ndaniel.foster@example.com",
            )),
        ],
        gap: 24,
    ))
    ->add(KeyValue::listing(
        new KeyValue('Signature deadline', new RichText(TextRun::strong('November 24, 2027 · 17:00 GMT'))),
        new KeyValue('Signing order', 'Daniel Foster, then Elena Martin'),
        new KeyValue('Verification', 'Email access code required'),
    ))
    ->add(new Steps(
        steps: ['Documents prepared', 'Your signature', 'Sender signature', 'Completed copy'],
        currentStep: 2,
    ))
    ->heading('Documents in this envelope', level: 2)
    ->add(new AttachmentList([
        new AttachmentItem('Master-Services-Agreement-v4.pdf', 1_420_000, $reviewUrl . '/msa'),
        new AttachmentItem('Data-Processing-Addendum.pdf', 680_000, $reviewUrl . '/dpa'),
        new AttachmentItem('Statement-of-Work-01.pdf', 394_000, $reviewUrl . '/sow-01'),
    ]))
    ->add(new Group(
        children: [
            new Heading('Ready to review and sign?', level: 2),
            new Text('The signing session highlights every required field and lets you download a copy before submitting your signature.'),
            new Button('Open and sign documents', $signatureUrl),
        ],
        backgroundColor: new Color('eff6ff'),
        padding: 24,
        borderColor: new Color('60a5fa'),
    ))
    ->add(new Alert(
        content: 'This secure link expires on November 24 at 17:00 GMT. It is personal to daniel.foster@example.com; request a new invitation if the link expires or the recipient details are incorrect.',
        heading: 'Link validity and security',
        variant: AlertVariant::Warning,
    ))
    ->add(new ButtonGroup([
        new Button('Review document package', $reviewUrl),
        new Button('Decline to sign', $signatureUrl . '/decline', ButtonVariant::Secondary),
    ]))
    ->divider()
    ->add(new Signature(new Contact(
        name: 'Elena Martin',
        position: 'Commercial Project Manager',
        organization: 'Northstar Engineering',
        email: 'elena.martin@example.com',
        phone: '+44 20 7946 0241',
    )))
    ->render();
