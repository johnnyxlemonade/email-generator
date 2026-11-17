<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Button;
use Lemonade\EmailGenerator\Component\ButtonGroup;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\ButtonGroupAlignment;
use Lemonade\EmailGenerator\Enum\ButtonVariant;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'ButtonGroup component')
    ->add(new ButtonGroup(
        buttons: [
            new Button('Approve', 'https://example.com/approve'),
            new Button('Review', 'https://example.com/review', ButtonVariant::Secondary),
        ],
        gap: 12,
        alignment: ButtonGroupAlignment::Center,
    ))
    ->render();
