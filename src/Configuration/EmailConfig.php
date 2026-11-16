<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Configuration;

use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\ValueObject\Color;

/**
 * Immutable email dimensions and colors used during rendering
 */
final readonly class EmailConfig
{
    /**
     * Sets the email width and rendering palette with standard defaults
     */
    public function __construct(
        private EmailWidth $width = EmailWidth::Standard,
        private Color $backgroundColor = new Color('#f1f5f9'),
        private Color $contentColor = new Color('#ffffff'),
        private Color $textColor = new Color('#1e293b'),
        private Color $accentColor = new Color('#2563eb'),
        private Color $buttonTextColor = new Color('#ffffff'),
    ) {}

    /**
     * Returns the maximum email width
     */
    public function width(): EmailWidth
    {
        return $this->width;
    }

    /**
     * Returns the color surrounding the email content
     */
    public function backgroundColor(): Color
    {
        return $this->backgroundColor;
    }

    /**
     * Returns the email content background color
     */
    public function contentColor(): Color
    {
        return $this->contentColor;
    }

    /**
     * Returns the default text color
     */
    public function textColor(): Color
    {
        return $this->textColor;
    }

    /**
     * Returns the color used for links and button backgrounds
     */
    public function accentColor(): Color
    {
        return $this->accentColor;
    }

    /**
     * Returns the button label color
     */
    public function buttonTextColor(): Color
    {
        return $this->buttonTextColor;
    }
}
