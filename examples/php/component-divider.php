<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\ValueObject\Color;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Divider component')
    ->text('Default divider')
    ->divider()
    ->text('Custom divider')
    ->divider(new Color('2563eb'), thickness: 3)
    ->render();
