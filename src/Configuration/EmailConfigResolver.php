<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Configuration;

use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Enum\FontFamily;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\Color;
use Lemonade\EmailGenerator\ValueObject\EmailPalette;

/**
 * Resolves the framework's ordered definitions, rejecting unsafe or misspelled settings
 */
final class EmailConfigResolver
{
    /**
     * Merges ordered definitions over defaults and rejects unknown or invalid options
     */
    public function resolve(EmailConfigDefinition ...$definitions): EmailConfig
    {
        $defaults = new EmailConfig();
        $width = $defaults->width();
        $fontFamily = $defaults->fontFamily();
        $colors = [
            'background_color' => $defaults->backgroundColor(),
            'content_color' => $defaults->contentColor(),
            'text_color' => $defaults->textColor(),
            'accent_color' => $defaults->accentColor(),
            'button_text_color' => $defaults->buttonTextColor(),
        ];
        foreach ($definitions as $definition) {
            foreach ($definition->toArray() as $key => $value) {
                if ($key === 'width') {
                    if (!is_int($value)) {
                        throw new InvalidInputException(ErrorCode::ConfigurationInvalidType, 'Email width must be an integer.');
                    }
                    $width = EmailWidth::tryFrom($value)
                        ?? throw new InvalidInputException(ErrorCode::WidthInvalid, 'Email width must be 480 (Compact), 600 (Standard) or 800 (Wide).');
                    continue;
                }
                if ($key === 'font_family') {
                    if (!is_string($value)) {
                        throw new InvalidInputException(ErrorCode::ConfigurationInvalidType, 'Email font family must be a string.');
                    }
                    $fontFamily = FontFamily::tryFrom($value)
                        ?? throw new InvalidInputException(
                            ErrorCode::FontFamilyInvalid,
                            'Email font family must be arial, verdana, tahoma, trebuchet, georgia, times_new_roman or courier_new.',
                        );
                    continue;
                }
                if (!is_string($key) || !array_key_exists($key, $colors)) {
                    throw new InvalidInputException(ErrorCode::ConfigurationUnknownOption, 'Unknown email_generator configuration option: ' . $key);
                }
                if (!is_string($value)) {
                    throw new InvalidInputException(ErrorCode::ConfigurationInvalidType, 'Email colors must be strings.');
                }
                $colors[$key] = new Color($value);
            }
        }

        return new EmailConfig(
            width: $width,
            palette: new EmailPalette(
                background: $colors['background_color'],
                content: $colors['content_color'],
                text: $colors['text_color'],
                accent: $colors['accent_color'],
                buttonText: $colors['button_text_color'],
            ),
            fontFamily: $fontFamily,
        );
    }
}
