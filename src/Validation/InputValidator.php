<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Validation;

use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Shared validation at content/configuration boundaries
 *
 * @internal
 */
final class InputValidator
{
    /**
     * Requires three or six hexadecimal digits with an optional hash prefix
     */
    public static function color(string $hex): void
    {
        if (preg_match('/\A#?([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\z/', $hex) !== 1) {
            throw new InvalidInputException(
                ErrorCode::ColorInvalid,
                'Color must contain three or six hexadecimal digits, optionally prefixed with #.',
            );
        }
    }

    /**
     * Rejects malformed UTF-8 and disallowed control characters in literal text
     */
    public static function text(string|Translation $value): void
    {
        if (
            is_string($value)
            && (preg_match('//u', $value) !== 1 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value) === 1)
        ) {
            throw new InvalidInputException(ErrorCode::TextInvalid, 'Text must be valid UTF-8 without control characters.');
        }
    }

    /**
     * Treats null and blank optional strings as absent after validating text
     */
    public static function optionalText(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        self::text($value);

        return $value;
    }

    /**
     * Validates text while accepting already validated rich text
     */
    public static function content(string|Translation|RichText $value): void
    {
        if (!$value instanceof RichText) {
            self::text($value);
        }
    }

    /**
     * Requires an ordered list of literal, translated or formatted text values
     *
     * @param array<mixed> $values
     */
    public static function textList(array $values): void
    {
        if (!array_is_list($values)) {
            throw new InvalidInputException(ErrorCode::TextListInvalid, 'Expected a list of text values.');
        }

        foreach ($values as $value) {
            if (!is_string($value) && !$value instanceof Translation && !$value instanceof RichText) {
                throw new InvalidInputException(
                    ErrorCode::TextListInvalid,
                    'Content values must be strings, Translation or RichText objects.',
                );
            }

            self::content($value);
        }
    }

    /**
     * Requires an integer within the inclusive bounds for the named setting
     */
    public static function range(int $value, int $min, int $max, string $name): void
    {
        if ($value < $min || $value > $max) {
            throw new InvalidInputException(
                ErrorCode::ValueOutOfRange,
                sprintf('%s must be between %d and %d.', $name, $min, $max),
            );
        }
    }

    /**
     * Requires a bounded language tag suitable for an explicit document locale
     */
    public static function locale(string $value): void
    {
        if (
            preg_match('/\A[a-zA-Z]{2,8}(?:[-_][a-zA-Z0-9]{1,8})*\z/', $value) !== 1
            || strlen($value) > 64
        ) {
            throw new InvalidInputException(ErrorCode::LocaleInvalid, 'Locale must be a language tag such as cs, en-US or pt_BR.');
        }
    }

    /**
     * Requires an absolute HTTP URL without credentials or unsafe characters
     */
    public static function url(string $value): void
    {
        $parts = parse_url($value);

        if (
            $parts === false
            || filter_var($value, FILTER_VALIDATE_URL) === false
            || !in_array(strtolower($parts['scheme'] ?? ''), ['https', 'http'], true)
            || !isset($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || preg_match('/[\x00-\x20\x7F<>"\'\\\\]|%(?:0[0-9a-f]|1[0-9a-f]|7f)/i', $value) === 1
        ) {
            throw new InvalidInputException(
                ErrorCode::UrlInvalid,
                'URL must be absolute HTTP(S), without credentials, whitespace or control characters.',
            );
        }
    }
}
