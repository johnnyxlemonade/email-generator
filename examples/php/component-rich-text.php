<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;

/** @var EmailGenerator $emails */

$content = new RichText(
    'Combine ',
    TextRun::strong('strong text'),
    ', ',
    TextRun::emphasis('emphasis'),
    ' and ',
    TextRun::link('a formatted link', 'https://example.com/docs', TextStyle::Strong),
    '.',
);

return $emails->create(locale: 'en', title: 'RichText content')
    ->heading($content, level: 2)
    ->text($content)
    ->render();
