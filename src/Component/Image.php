<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * An HTTP image with alternative text and bounded display dimensions
 */
final readonly class Image implements ComponentInterface
{
    /**
     * Validates the image URL, alternative text and display dimensions
     */
    public function __construct(
        private string $url,
        private string|Translation $alt,
        private int $width = 536,
        private ?int $height = null,
    ) {
        InputValidator::url($url);
        InputValidator::text($alt);
        InputValidator::range($width, 1, 2000, 'Image width');

        if ($height !== null) {
            InputValidator::range($height, 1, 2000, 'Image height');
        }
    }

    /**
     * Returns the validated image URL
     */
    public function url(): string
    {
        return $this->url;
    }

    /**
     * Returns the literal or translated alternative text
     */
    public function alt(): string|Translation
    {
        return $this->alt;
    }

    /**
     * Returns the requested maximum display width in pixels
     */
    public function width(): int
    {
        return $this->width;
    }

    /**
     * Returns the optional height used to preserve the requested aspect ratio
     */
    public function height(): ?int
    {
        return $this->height;
    }
}
