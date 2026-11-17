<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * A validated CSS color; arbitrary CSS never enters the renderer
 */
final readonly class Color
{
    private string $hex;

    /**
     * Validates hexadecimal input and normalizes it to a lowercase six-digit CSS color
     */
    public function __construct(string $hex)
    {
        InputValidator::color($hex);

        $digits = strtolower(ltrim($hex, '#'));

        if (strlen($digits) === 3) {
            $digits = $digits[0] . $digits[0] . $digits[1] . $digits[1] . $digits[2] . $digits[2];
        }

        $this->hex = '#' . $digits;
    }

    /**
     * Returns the normalized hexadecimal color including its hash prefix
     */
    public function hex(): string
    {
        return $this->hex;
    }
}
