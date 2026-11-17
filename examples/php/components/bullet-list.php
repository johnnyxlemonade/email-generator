<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'BulletList component')
    ->list(['Reusable components', 'Immutable documents', 'Escaped content'])
    ->list(['Create the document', 'Add components', 'Render HTML'], ordered: true)
    ->render();
