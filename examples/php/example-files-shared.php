<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\AttachmentList;
use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\Component\BulletList;
use Lemonade\EmailGenerator\Component\Columns;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\Heading;
use Lemonade\EmailGenerator\Component\Quote;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\BadgeVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\AttachmentItem;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$folderUrl = 'https://example.com/workspaces/harbor-renewal/folders/site-survey';

return $emails->create(
    locale: 'en',
    title: 'Priya Shah shared 4 files with you',
    preheader: 'Site survey files for the Harbor Renewal project are available until December 15.',
)->text(new RichText(
    TextRun::strong('FIELDSPACE'),
    "\nSecure project sharing",
))->divider(new Color('0891b2'), thickness: 3)
    ->heading('Priya Shah shared files with you')
    ->text('You now have access to the latest site survey package in the Harbor Renewal workspace. The files are grouped in one shared folder so comments and revisions stay together.')
    ->add(new Group(
        children: [
            new Badge('Can edit', BadgeVariant::Success),
            new Text('PROJECT / FOLDER'),
            new Heading('Harbor Renewal', level: 2),
            new Text(new RichText(
                TextRun::strong('03 — Site survey and constraints'),
                "\nShared by Priya Shah · Project Architect",
            )),
        ],
        backgroundColor: new Color('ecfeff'),
        padding: 24,
        borderColor: new Color('67e8f9'),
    ))
    ->heading('Shared files', level: 2)
    ->add(new AttachmentList([
        new AttachmentItem('Existing-conditions-survey.pdf', 8_460_000, $folderUrl . '/existing-conditions'),
        new AttachmentItem('Site-levels-and-datum.dwg', 12_840_000, $folderUrl . '/site-levels'),
        new AttachmentItem('Utilities-markup-v3.pdf', 3_120_000, $folderUrl . '/utilities-markup'),
        new AttachmentItem('Survey-photo-index.xlsx', 742_000, $folderUrl . '/photo-index'),
    ]))
    ->add(new Columns(
        first: [new Group(
            children: [
                new Heading('Your access', level: 3),
                new Text(new RichText(TextRun::strong('Edit and comment'))),
                new Text('You may upload new versions, add comments and invite existing project members.'),
            ],
            backgroundColor: new Color('f0fdf4'),
            padding: 18,
            borderColor: new Color('86efac'),
        )],
        second: [new Group(
            children: [
                new Heading('Available until', level: 3),
                new Text(new RichText(TextRun::strong('December 15, 2027'))),
                new Text('Access ends at 23:59 GMT unless Priya extends the share.'),
            ],
            backgroundColor: new Color('fff7ed'),
            padding: 18,
            borderColor: new Color('fdba74'),
        )],
        gap: 16,
    ))
    ->add(new Quote(
        content: 'Please review the utilities markup before Thursday’s coordination call. Add questions directly to the relevant drawing so the surveyor can respond in context.',
        attribution: 'Priya Shah · sharing note',
    ))
    ->add(new Alert(
        content: new RichText(
            'Files remain in the project workspace. Downloaded copies will not receive later revisions; use the ',
            TextRun::strong('Latest version'),
            ' label before sharing them externally.',
        ),
        heading: 'Working with shared files',
        variant: AlertVariant::Info,
    ))
    ->add(new BulletList([
        'Comments are visible to all Harbor Renewal project members.',
        'Uploaded revisions retain the existing approval and version history.',
        'External forwarding is disabled for this folder.',
    ]))
    ->button('Open shared documents', $folderUrl)
    ->text('This invitation was sent to alex.morgan@example.com. Ask Priya to share the folder again if another person needs access.')
    ->render();
