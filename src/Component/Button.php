<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * A validated link presented as an email call-to-action button
 */
final readonly class Button implements ComponentInterface
{
    /**
     * Validates the button label and absolute HTTP link
     */
    public function __construct(private string|Translation $label, private string $url)
    {
        InputValidator::text($label);
        InputValidator::url($url);
    }

    /**
     * Returns the literal or translated button label
     */
    public function label(): string|Translation
    {
        return $this->label;
    }

    /**
     * Returns the validated link destination
     */
    public function url(): string
    {
        return $this->url;
    }
}
