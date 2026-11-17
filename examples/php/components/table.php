<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Table component')
    ->table(
        headers: ['Item', 'Status'],
        rows: [
            ['Account', 'Active'],
            ['Plan', new RichText(TextRun::strong('Team'))],
        ],
    )
    ->render();
