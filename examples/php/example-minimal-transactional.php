<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Alert;
use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\Component\Group;
use Lemonade\EmailGenerator\Component\KeyValue;
use Lemonade\EmailGenerator\Component\Text;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Enum\ButtonVariant;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

$downloadUrl = 'https://example.com/exports/EXP-2027-1048/download';
$exportsUrl = 'https://example.com/account/exports';

return $emails->create(
    locale: 'en',
    title: 'Your customer export is ready',
    preheader: 'Download customer-export-october.csv before the link expires.',
)->spacer(24)
    ->text('NORTHSTAR', alignment: HorizontalAlignment::Center)
    ->spacer(12)
    ->heading('Your export is ready', alignment: HorizontalAlignment::Center)
    ->text(
        'We finished preparing the customer data export you requested. The file is ready to download for the next seven days.',
        alignment: HorizontalAlignment::Center,
    )
    ->spacer(8)
    ->add(new Group(
        children: [KeyValue::listing(
            new KeyValue('File', new RichText(TextRun::strong('customer-export-october.csv'))),
            new KeyValue('Records', '18,429 customers'),
            new KeyValue('Size', '6.8 MB'),
            new KeyValue('Expires', 'October 31, 2027 at 16:20 UTC'),
        )],
        backgroundColor: new Color('f8fafc'),
        padding: 20,
        borderColor: new Color('e2e8f0'),
    ))
    ->add(new ButtonGroup(
        buttons: [
            new Button('Download CSV', $downloadUrl),
            new Button('View all exports', $exportsUrl, ButtonVariant::Secondary),
        ],
        alignment: HorizontalAlignment::Center,
    ))
    ->add(new Alert(
        content: 'The download contains personal data. Store it securely and delete local copies when they are no longer needed.',
        variant: AlertVariant::Info,
    ))
    ->divider()
    ->text(
        'Export EXP-2027-1048 · Requested by alex.morgan@example.com',
        alignment: HorizontalAlignment::Center,
    )
    ->render();
