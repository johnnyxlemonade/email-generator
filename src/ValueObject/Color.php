<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;

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
        $this->validate($hex);
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

    /**
     * Rejects values other than three or six hexadecimal digits with an optional hash
     */
    private function validate(string $hex): void
    {
        if (preg_match('/\A#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\z/', $hex) !== 1) {
            throw new InvalidInputException(ErrorCode::ColorInvalid, 'Color must contain three or six hexadecimal digits, optionally prefixed with #.');
        }
    }
}
