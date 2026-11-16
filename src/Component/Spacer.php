<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Validation\InputValidator;

/**
 * An explicit vertical gap measured in pixels
 */
final readonly class Spacer implements ComponentInterface
{
    /**
     * Validates the requested vertical gap
     */
    public function __construct(private int $height = 16)
    {
        InputValidator::range($height, 1, 1000, 'Spacer height');
    }

    /**
     * Returns the vertical gap in pixels
     */
    public function height(): int
    {
        return $this->height;
    }
}
