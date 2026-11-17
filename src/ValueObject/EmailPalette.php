<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\ValueObject;

/**
 * Immutable colors shared across the rendered email
 */
final readonly class EmailPalette
{
    /**
     * Sets the document, content, text, accent and button label colors
     */
    public function __construct(
        private Color $background = new Color('#f1f5f9'),
        private Color $content = new Color('#ffffff'),
        private Color $text = new Color('#1e293b'),
        private Color $accent = new Color('#2563eb'),
        private Color $buttonText = new Color('#ffffff'),
    ) {}

    /**
     * Returns the color surrounding the email content
     */
    public function background(): Color
    {
        return $this->background;
    }

    /**
     * Returns the email content background color
     */
    public function content(): Color
    {
        return $this->content;
    }

    /**
     * Returns the default text color
     */
    public function text(): Color
    {
        return $this->text;
    }

    /**
     * Returns the color used for links and button backgrounds
     */
    public function accent(): Color
    {
        return $this->accent;
    }

    /**
     * Returns the button label color
     */
    public function buttonText(): Color
    {
        return $this->buttonText;
    }
}
