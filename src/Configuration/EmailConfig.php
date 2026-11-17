<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Configuration;

use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Enum\FontFamily;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\EmailPalette;

/**
 * Immutable email dimensions, colors and typography used during rendering
 */
final readonly class EmailConfig
{
    /**
     * Sets the email width, rendering palette and font stack
     */
    public function __construct(
        private EmailWidth $width = EmailWidth::Standard,
        private EmailPalette $palette = new EmailPalette(),
        private FontFamily $fontFamily = FontFamily::Arial,
    ) {}

    /**
     * Returns the maximum email width
     */
    public function width(): EmailWidth
    {
        return $this->width;
    }

    /**
     * Returns the complete rendering color palette
     */
    public function palette(): EmailPalette
    {
        return $this->palette;
    }

    /**
     * Returns the color surrounding the email content
     */
    public function backgroundColor(): Color
    {
        return $this->palette->background();
    }

    /**
     * Returns the email content background color
     */
    public function contentColor(): Color
    {
        return $this->palette->content();
    }

    /**
     * Returns the default text color
     */
    public function textColor(): Color
    {
        return $this->palette->text();
    }

    /**
     * Returns the color used for links and button backgrounds
     */
    public function accentColor(): Color
    {
        return $this->palette->accent();
    }

    /**
     * Returns the button label color
     */
    public function buttonTextColor(): Color
    {
        return $this->palette->buttonText();
    }

    /**
     * Returns the CSS font-family stack
     */
    public function fontFamily(): FontFamily
    {
        return $this->fontFamily;
    }
}
