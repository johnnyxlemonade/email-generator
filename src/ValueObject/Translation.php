<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * An immutable translation reference resolved in the document's locale
 */
final readonly class Translation
{
    /**
     * @var array<string, scalar|null>
     */
    private array $replacements;

    /**
     * Validates translation parameters and detaches external PHP references
     *
     * @param array<string, scalar|null> $replacements
     */
    public function __construct(private string $key, array $replacements = [])
    {
        InputValidator::text($key);
        if (trim($key) === '') {
            throw new InvalidInputException(ErrorCode::TranslationKeyInvalid, 'Translation key must not be empty.');
        }

        $snapshot = [];

        foreach ($replacements as $name => $value) {
            if (!is_string($name) || (!is_scalar($value) && $value !== null)) {
                throw new InvalidInputException(
                    ErrorCode::TranslationReplacementsInvalid,
                    'Translation replacements must map string names to scalar values or null.',
                );
            }

            InputValidator::text($name);
            InputValidator::text((string) $value);

            $snapshot[$name] = $value;
        }

        $this->replacements = $snapshot;
    }

    /**
     * Returns the translation key
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * Returns the named scalar replacement values
     *
     * @return array<string, scalar|null>
     */
    public function replacements(): array
    {
        return $this->replacements;
    }
}
