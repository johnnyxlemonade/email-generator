<?php

declare(strict_types=1);

use Lemonade\EmailGenerator\Component\Badge;
use Lemonade\EmailGenerator\EmailGenerator;
use Lemonade\EmailGenerator\Enum\BadgeVariant;

/** @var EmailGenerator $emails */

return $emails->create(locale: 'en', title: 'Badge component')
    ->add(
        new Badge('Neutral'),
        new Badge('Information', BadgeVariant::Info),
        new Badge('Completed', BadgeVariant::Success),
        new Badge('Review needed', BadgeVariant::Warning),
        new Badge('Action required', BadgeVariant::Danger),
    )
    ->render();
