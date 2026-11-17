<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * A labeled absolute HTTP link to a social profile
 */
final readonly class SocialLink
{
    /**
     * Validates the visible label and profile URL
     */
    public function __construct(private string|Translation $label, private string $url)
    {
        InputValidator::text($label);
        InputValidator::url($url);
    }

    /**
     * Returns the literal or translated profile label
     */
    public function label(): string|Translation
    {
        return $this->label;
    }

    /**
     * Returns the validated profile URL
     */
    public function url(): string
    {
        return $this->url;
    }
}
