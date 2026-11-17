<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * The supported semantic appearances for an alert
 */
enum AlertVariant: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
}
