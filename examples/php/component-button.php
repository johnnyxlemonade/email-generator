<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\ButtonVariant;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Button component')
    ->button('Primary action', 'https://example.com/primary')
    ->button('Secondary action', 'https://example.com/secondary', ButtonVariant::Secondary)
    ->render();
