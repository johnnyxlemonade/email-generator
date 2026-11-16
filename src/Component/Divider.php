<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Component;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Color;

/**
 * A horizontal separator with a validated color and pixel thickness
 */
final readonly class Divider implements ComponentInterface
{
    /**
     * Sets the separator color and validates its pixel thickness
     */
    public function __construct(private Color $color = new Color('#e2e8f0'), private int $thickness = 1)
    {
        InputValidator::range($thickness, 1, 10, 'Divider thickness');
    }

    /**
     * Returns the separator color
     */
    public function color(): Color
    {
        return $this->color;
    }

    /**
     * Returns the separator thickness in pixels
     */
    public function thickness(): int
    {
        return $this->thickness;
    }
}
