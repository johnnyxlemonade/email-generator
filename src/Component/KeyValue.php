<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * A label and value composed as an existing two-cell data table
 */
final readonly class KeyValue implements CompositionInterface
{
    /**
     * Validates both text values while preserving typed formatting and translations
     */
    public function __construct(
        private string|Translation|RichText $label,
        private string|Translation|RichText $value,
    ) {
        InputValidator::content($label);
        InputValidator::content($value);
    }

    /**
     * Returns the descriptive label
     */
    public function label(): string|Translation|RichText
    {
        return $this->label;
    }

    /**
     * Returns the associated value
     */
    public function value(): string|Translation|RichText
    {
        return $this->value;
    }

    /**
     * Composes a single pair using the shared table appearance
     *
     * @return iterable<Table>
     */
    public function components(): iterable
    {
        yield self::listing($this);
    }

    /**
     * Combines one or more pairs into one table with aligned columns
     */
    public static function listing(self $first, self ...$rest): Table
    {
        $rows = [[$first->label(), $first->value()]];
        foreach ($rest as $pair) {
            $rows[] = [$pair->label(), $pair->value()];
        }

        return new Table(headers: [], rows: $rows, compact: true);
    }
}
