<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Heading component')
    ->heading('Primary heading')
    ->heading('Centered section heading', level: 2, alignment: HorizontalAlignment::Center)
    ->heading('Right-aligned subsection heading', level: 3, alignment: HorizontalAlignment::Right)
    ->render();
