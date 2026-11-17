<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Text component')
    ->text("Literal text preserves\nline breaks and escapes <HTML>.")
    ->text(new Translation('emails.body'))
    ->render();
