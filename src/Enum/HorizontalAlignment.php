<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Horizontal alignment of component content
 */
enum HorizontalAlignment: string
{
    case Left = 'left';
    case Center = 'center';
    case Right = 'right';
}
