<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Enum\HorizontalAlignment;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Immutable heading content with a level from one to six
 */
final readonly class Heading implements ComponentInterface
{
    /**
     * Validates heading content and its level from one to six
     */
    public function __construct(
        private string|Translation|RichText $content,
        private int $level = 1,
        private HorizontalAlignment $alignment = HorizontalAlignment::Left,
    ) {
        InputValidator::content($content);
        InputValidator::range($level, 1, 6, 'Heading level');
    }

    /**
     * Returns the literal, translated or formatted heading content
     */
    public function content(): string|Translation|RichText
    {
        return $this->content;
    }

    /**
     * Returns the HTML heading level
     */
    public function level(): int
    {
        return $this->level;
    }

    /**
     * Returns the heading text alignment
     */
    public function alignment(): HorizontalAlignment
    {
        return $this->alignment;
    }
}
