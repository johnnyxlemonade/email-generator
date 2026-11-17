<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Enum\AlertVariant;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * A semantic information block composed from a styled group and text primitives
 */
final readonly class Alert implements CompositionInterface
{
    /**
     * Validates the required content and optional heading
     */
    public function __construct(
        private string|Translation|RichText $content,
        private string|Translation|RichText|null $heading = null,
        private AlertVariant $variant = AlertVariant::Info,
    ) {
        InputValidator::content($content);
        if ($heading !== null) {
            InputValidator::content($heading);
        }
    }

    /**
     * Returns the required alert content
     */
    public function content(): string|Translation|RichText
    {
        return $this->content;
    }

    /**
     * Returns the optional alert heading
     */
    public function heading(): string|Translation|RichText|null
    {
        return $this->heading;
    }

    /**
     * Returns the semantic alert variant
     */
    public function variant(): AlertVariant
    {
        return $this->variant;
    }

    /**
     * Composes the alert inside a variant-specific visual group
     *
     * @return iterable<Group>
     */
    public function components(): iterable
    {
        $children = [];
        if ($this->heading !== null) {
            $children[] = new Heading($this->heading, level: 3);
        }
        $children[] = new Text($this->content);
        [$background, $border] = match ($this->variant) {
            AlertVariant::Info => ['eff6ff', '93c5fd'],
            AlertVariant::Success => ['f0fdf4', '86efac'],
            AlertVariant::Warning => ['fffbeb', 'fcd34d'],
            AlertVariant::Error => ['fef2f2', 'fca5a5'],
        };

        yield new Group(
            children: $children,
            backgroundColor: new Color($background),
            padding: 16,
            borderColor: new Color($border),
        );
    }
}
