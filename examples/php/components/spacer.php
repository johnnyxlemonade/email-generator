<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\EmailGenerator;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Spacer component')
    ->text('Content before a 32-pixel spacer.')
    ->spacer(height: 32)
    ->text('Content after the spacer.')
    ->render();
