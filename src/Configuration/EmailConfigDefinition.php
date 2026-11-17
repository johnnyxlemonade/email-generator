<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Configuration;

use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Enum\FontFamily;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\Framework\Core\Config\Definition\AbstractConfigDefinition;

/**
 * Builds framework configuration entries for email dimensions, colors and typography
 */
final class EmailConfigDefinition extends AbstractConfigDefinition
{
    /**
     * Creates an empty definition ready for typed configuration entries
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * Returns the framework configuration module key
     */
    public static function moduleKey(): string
    {
        return 'email_generator';
    }

    /**
     * Sets the maximum email width and returns this definition
     */
    public function width(EmailWidth $width): self
    {
        return $this->set('width', $width->value);
    }

    /**
     * Sets the surrounding background color and returns this definition
     */
    public function backgroundColor(Color $color): self
    {
        return $this->set('background_color', $color->hex());
    }

    /**
     * Sets the content background color and returns this definition
     */
    public function contentColor(Color $color): self
    {
        return $this->set('content_color', $color->hex());
    }

    /**
     * Sets the default text color and returns this definition
     */
    public function textColor(Color $color): self
    {
        return $this->set('text_color', $color->hex());
    }

    /**
     * Sets the link and button background color and returns this definition
     */
    public function accentColor(Color $color): self
    {
        return $this->set('accent_color', $color->hex());
    }

    /**
     * Sets the button label color and returns this definition
     */
    public function buttonTextColor(Color $color): self
    {
        return $this->set('button_text_color', $color->hex());
    }

    /**
     * Sets the CSS font-family stack and returns this definition
     */
    public function fontFamily(FontFamily $fontFamily): self
    {
        return $this->set('font_family', $fontFamily->value);
    }
}
