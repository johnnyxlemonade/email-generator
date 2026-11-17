<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * Validated HTTP image source and alternative text
 */
final readonly class ImageSource
{
    /**
     * Validates the image URL and alternative text
     */
    public function __construct(
        private string $url,
        private string|Translation $alt = '',
    ) {
        InputValidator::url($url);
        InputValidator::text($alt);
    }

    /**
     * Returns the validated absolute HTTP image URL
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Returns the image alternative text
     */
    public function alt(): string|Translation
    {
        return $this->alt;
    }
}
