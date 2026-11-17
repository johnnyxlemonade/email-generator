<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

use Lemonade\EmailGenerator\ValueObject\AlertPalette;
use Lemonade\EmailGenerator\ValueObject\Color;

/**
 * The supported semantic appearances for an alert
 */
enum AlertVariant: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';

    /**
     * Returns the semantic background and border colors for this variant
     */
    public function palette(): AlertPalette
    {
        return match ($this) {
            self::Info => new AlertPalette(
                new Color('#eff6ff'),
                new Color('#93c5fd'),
            ),
            self::Success => new AlertPalette(
                new Color('#f0fdf4'),
                new Color('#86efac'),
            ),
            self::Warning => new AlertPalette(
                new Color('#fffbeb'),
                new Color('#fcd34d'),
            ),
            self::Error => new AlertPalette(
                new Color('#fef2f2'),
                new Color('#fca5a5'),
            ),
        };
    }
}
