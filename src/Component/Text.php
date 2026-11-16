<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Immutable paragraph content supporting translations and inline formatting
 */
final readonly class Text implements ComponentInterface
{
    /**
     * Validates literal or translated content while accepting immutable rich text
     */
    public function __construct(private string|Translation|RichText $content)
    {
        InputValidator::content($content);
    }

    /**
     * Returns the paragraph content without rendering or translating it
     */
    public function content(): string|Translation|RichText
    {
        return $this->content;
    }
}
