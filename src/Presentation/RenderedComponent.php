<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation;

/**
 * Separates component HTML from the dispatcher's normal or fixed-height row layout
 *
 * @internal
 */
final readonly class RenderedComponent
{
    /**
     * Sets content and an optional exact row height that replaces normal spacing
     */
    public function __construct(private string $html, private ?int $height = null) {}

    /**
     * Returns component HTML without a shared layout row
     */
    public function html(): string
    {
        return $this->html;
    }

    /**
     * Returns the exact row height, or null for normal text layout
     */
    public function height(): ?int
    {
        return $this->height;
    }
}
