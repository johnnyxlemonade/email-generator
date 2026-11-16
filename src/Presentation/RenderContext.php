<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

use Lemonade\EmailGenerator\Configuration\EmailConfig;
use Lemonade\EmailGenerator\ValueObject\Translation;
use Lemonade\Framework\Localization\TranslatorInterface;

/**
 * Provides configuration, locale-specific translations and shared HTML escaping
 *
 * @internal
 */
final readonly class RenderContext
{
    /**
     * Sets the dependencies and explicit locale for one rendering
     */
    public function __construct(private TranslatorInterface $translator, private EmailConfig $config, private string $locale) {}

    /**
     * Returns the immutable email configuration
     */
    public function config(): EmailConfig
    {
        return $this->config;
    }

    /**
     * Translates text in this context and escapes the result
     */
    public function text(string|Translation $value): string
    {
        $text = $value instanceof Translation
            ? $this->translator->get($value->key(), $value->replacements(), $this->locale)
            : $value;

        return self::escape($text);
    }

    /**
     * Escapes a dynamic value for HTML text and quoted attributes
     */
    public static function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }
}
