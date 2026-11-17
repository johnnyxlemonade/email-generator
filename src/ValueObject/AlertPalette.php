<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

/**
 * Immutable background and border colors for an alert variant
 */
final readonly class AlertPalette
{
    /**
     * Sets the alert background and border colors
     */
    public function __construct(
        private Color $background,
        private Color $border,
    ) {}

    /**
     * Returns the alert background color
     */
    public function background(): Color
    {
        return $this->background;
    }

    /**
     * Returns the alert border color
     */
    public function border(): Color
    {
        return $this->border;
    }
}
