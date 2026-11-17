<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Text\TextRun;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Text component')
    ->text("Literal text preserves\nline breaks and escapes <HTML>.")
    ->text(new Translation('emails.body'), alignment: HorizontalAlignment::Center)
    ->text(new RichText(TextRun::strong('Right-aligned'), ' formatted text.'), alignment: HorizontalAlignment::Right)
    ->render();
