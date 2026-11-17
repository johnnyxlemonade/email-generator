<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Horizontal alignment of actions inside a button group
 */
enum ButtonGroupAlignment: string
{
    case Left = 'left';
    case Center = 'center';
    case Right = 'right';
}
