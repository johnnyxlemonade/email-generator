<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$replyUrl = 'https://example.com/support/tickets/SUP-48219/reply';
$ticketUrl = 'https://example.com/support/tickets/SUP-48219';
$helpUrl = 'https://example.com/help/importing-data';

return $emails->create(
    locale: 'en',
    title: 'Support replied to ticket SUP-48219',
    preheader: 'Nina from support has replied about your CSV import issue.',
)->text(new RichText(
    TextRun::strong('LEMONADE SUPPORT'),
    "\nTicket update",
))->divider(new Color('7c3aed'), thickness: 3)
    ->add(new Badge('Waiting for your reply', BadgeVariant::Info))
    ->heading('Nina replied to your support request')
    ->text('Hi Sam, there is a new response on your ticket about importing customer data. The conversation is included below so you can continue without opening the support portal first.')
    ->add(KeyValue::listing(
        new KeyValue('Ticket', new RichText(TextRun::strong('SUP-48219'))),
        new KeyValue('Subject', 'CSV import stops at 74%'),
        new KeyValue('Priority', 'Normal'),
        new KeyValue('Updated', 'June 14, 2027 · 10:26 CEST'),
    ))
    ->heading('Your original message', level: 2)
    ->add(new Quote(
        content: 'Our customer import consistently stops at 74%. The file contains 8,420 rows and passes the preview validation. We tried twice this morning with the same result.',
        attribution: 'Sam Rivera · June 14, 2027 at 08:51 CEST',
    ))
    ->heading('Support response', level: 2)
    ->add(new Group(
        children: [
            new Text(new RichText(
                TextRun::strong('Nina K. · Customer Support'),
                "\nJune 14, 2027 at 10:26 CEST",
            )),
            new Text("Hi Sam,\n\nthanks for sending the details. I found one row with an unmatched quote in the company_notes column. The preview accepts the cell as text, but the background import reads the following rows as one large value and stops when it reaches the size limit."),
            new Text(new RichText(
                'Please correct row ',
                TextRun::strong('6,231'),
                ' or export the file again with quotes escaped. I have left the failed import available for seven days so you can compare it with the corrected file.',
            )),
            new Text('Reply with the new import ID if the second attempt still does not complete, and I will review the processing log.'),
        ],
        backgroundColor: new Color('f5f3ff'),
        padding: 20,
        borderColor: new Color('c4b5fd'),
    ))
    ->add(new Group(
        children: [
            new Heading('Suggested next steps', level: 3),
            new BulletList([
                'Open row 6,231 and correct the unmatched quote in company_notes.',
                'Save the file as UTF-8 CSV with quoted values escaped.',
                'Start a new import and keep the resulting import ID.',
            ], ordered: true),
            new Text(new RichText(TextRun::link('Read the CSV import guide', $helpUrl))),
        ],
        backgroundColor: new Color('f8fafc'),
        padding: 16,
        borderColor: new Color('e2e8f0'),
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Reply to Nina', $replyUrl),
            new Button('View full ticket', $ticketUrl, ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: HorizontalAlignment::Left,
    ))
    ->divider(new Color('ddd6fe'))
    ->text(new RichText(
        TextRun::strong('Lemonade Support'),
        "\nsupport@example.com · Monday–Friday, 08:00–18:00 CEST",
        "\nReplies to this demonstration email are not monitored.",
    ))
    ->render();
