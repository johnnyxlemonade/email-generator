<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Supported maximum email widths in pixels
 */
enum EmailWidth: int
{
    case Compact = 480;
    case Standard = 600;
    case Wide = 800;
    case Desktop = 1200;
}
