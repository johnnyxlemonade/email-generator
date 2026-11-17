<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Table component')
    ->table(
        headers: ['Item', 'Quantity', 'Amount'],
        rows: [
            ['Team plan', '3', '$120.00'],
            [new RichText(TextRun::strong('Support')), '1', '$25.00'],
        ],
        alignment: HorizontalAlignment::Center,
        columnAlignments: [
            0 => HorizontalAlignment::Left,
            2 => HorizontalAlignment::Right,
        ],
    )
    ->render();
