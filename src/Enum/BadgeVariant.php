<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * The supported status appearances for a badge
 */
enum BadgeVariant: string
{
    case Neutral = 'neutral';
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
}
