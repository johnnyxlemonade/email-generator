<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Text;

use Lemonade\EmailGenerator\Enum\TextStyle;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * A text-only span with a finite style and an optional validated link
 */
final readonly class TextRun
{
    /**
     * Validates the text and optional link while retaining the selected inline style
     */
    public function __construct(
        private string|Translation $content,
        private TextStyle $style = TextStyle::Plain,
        private ?string $url = null,
    ) {
        InputValidator::text($content);
        if ($url !== null) {
            InputValidator::url($url);
        }
    }

    /**
     * Creates a run with strong text styling
     */
    public static function strong(string|Translation $content): self
    {
        return new self($content, TextStyle::Strong);
    }

    /**
     * Creates a run with emphasis styling
     */
    public static function emphasis(string|Translation $content): self
    {
        return new self($content, TextStyle::Emphasis);
    }

    /**
     * Creates a validated link with the selected text style
     */
    public static function link(string|Translation $label, string $url, TextStyle $style = TextStyle::Plain): self
    {
        return new self($label, $style, $url);
    }

    /**
     * Returns the literal or translated text
     */
    public function content(): string|Translation
    {
        return $this->content;
    }

    /**
     * Returns the selected inline text style
     */
    public function style(): TextStyle
    {
        return $this->style;
    }

    /**
     * Returns the optional validated link destination
     */
    public function url(): ?string
    {
        return $this->url;
    }
}
