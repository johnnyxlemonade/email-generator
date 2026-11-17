<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Enum;

/**
 * Email-safe font stacks available to the renderer
 */
enum FontFamily: string
{
    case Arial = 'arial';
    case Verdana = 'verdana';
    case Tahoma = 'tahoma';
    case Trebuchet = 'trebuchet';
    case Georgia = 'georgia';
    case TimesNewRoman = 'times_new_roman';
    case CourierNew = 'courier_new';

    /**
     * Returns the complete CSS fallback stack
     */
    public function css(): string
    {
        return match ($this) {
            self::Arial => 'Arial,Helvetica,sans-serif',
            self::Verdana => 'Verdana,Geneva,sans-serif',
            self::Tahoma => 'Tahoma,Verdana,sans-serif',
            self::Trebuchet => 'Trebuchet MS,Arial,sans-serif',
            self::Georgia => 'Georgia,Times New Roman,serif',
            self::TimesNewRoman => 'Times New Roman,Times,serif',
            self::CourierNew => 'Courier New,Courier,monospace',
        };
    }
}
