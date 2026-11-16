<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\CompositionInterface;
use Lemonade\EmailGenerator\Text\RichText;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Highlighted text composed inside a consistently styled visual group
 */
final readonly class Quote implements CompositionInterface
{
    /**
     * Validates the quotation and optional attribution
     */
    public function __construct(
        private string|Translation|RichText $content,
        private string|Translation|RichText|null $attribution = null,
    ) {
        InputValidator::content($content);
        if ($attribution !== null) {
            InputValidator::content($attribution);
        }
    }

    /**
     * Returns the highlighted content
     */
    public function content(): string|Translation|RichText
    {
        return $this->content;
    }

    /**
     * Returns the optional source or author
     */
    public function attribution(): string|Translation|RichText|null
    {
        return $this->attribution;
    }

    /**
     * Composes paragraphs inside the standard bordered group
     *
     * @return iterable<Group>
     */
    public function components(): iterable
    {
        $children = [new Text($this->content)];
        if ($this->attribution !== null) {
            $children[] = new Text($this->attribution);
        }

        yield new Group(
            children: $children,
            backgroundColor: new Color('f8fafc'),
            padding: 16,
            borderColor: new Color('cbd5e1'),
        );
    }
}
