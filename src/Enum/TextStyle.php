<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Supported combinations of strong text and emphasis for inline runs
 */
enum TextStyle
{
    case Plain;
    case Strong;
    case Emphasis;
    case StrongEmphasis;
}
