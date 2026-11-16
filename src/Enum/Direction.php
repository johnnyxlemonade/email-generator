<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Supported reading directions for an email document
 */
enum Direction: string
{
    case Ltr = 'ltr';
    case Rtl = 'rtl';
}
