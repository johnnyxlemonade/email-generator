<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Configuration;

use Lemonade\EmailGenerator\Enum\EmailWidth;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Exception\InvalidInputException;
use Lemonade\EmailGenerator\ValueObject\Color;

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
            backgroundColor: $colors['background_color'],
            contentColor: $colors['content_color'],
            textColor: $colors['text_color'],
            accentColor: $colors['accent_color'],
            buttonTextColor: $colors['button_text_color'],
        );
    }
}
