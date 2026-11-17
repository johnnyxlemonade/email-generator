<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Image component')
    ->image(
        url: 'https://placehold.co/800x320/png?text=Email+Image',
        alt: 'Decorative email example',
        width: 536,
        height: 214,
    )
    ->render();
