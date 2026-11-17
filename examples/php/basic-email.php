<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/** @var EmailGenerator $emails */

return $emails->create(
    locale: 'en',
    title: new Translation('emails.title'),
    preheader: new Translation('emails.preview'),
)->heading(new Translation('emails.welcome', ['name' => 'Alex']))
    ->text(new Translation('emails.body'))
    ->button(new Translation('emails.activate'), 'https://example.com/activate?token=demo&source=email')
    ->spacer()
    ->divider()
    ->text(new Translation('emails.footer'))
    ->render();
