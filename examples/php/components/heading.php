<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Heading component')
    ->heading('Primary heading')
    ->heading('Section heading', level: 2)
    ->heading('Subsection heading', level: 3)
    ->render();
