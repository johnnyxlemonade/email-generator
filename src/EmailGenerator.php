<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator;

use Lemonade\EmailGenerator\Enum\Direction;
use Lemonade\EmailGenerator\Presentation\HtmlRenderer;
use Lemonade\EmailGenerator\Validation\InputValidator;
use Lemonade\EmailGenerator\ValueObject\Translation;

/**
 * Creates independent email documents with a shared renderer and default locale
 */
final readonly class EmailGenerator
{
    /**
     * Sets the shared renderer and validates the default document locale
     */
    public function __construct(private HtmlRenderer $renderer, private string $defaultLocale = 'en')
    {
        InputValidator::locale($defaultLocale);
    }

    /**
     * Creates an independent document using the explicit or default locale
     */
    public function create(
        ?string $locale = null,
        string|Translation $title = '',
        string|Translation $preheader = '',
        Direction $direction = Direction::Ltr,
    ): Document {
        return new Document(
            renderer: $this->renderer,
            locale: $locale ?? $this->defaultLocale,
            title: $title,
            preheader: $preheader,
            direction: $direction,
        );
    }
}
