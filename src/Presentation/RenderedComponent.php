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
     * Sets content, optional exact row height and semantic spacing after the row
     */
    public function __construct(
        private string $html,
        private ?int $height = null,
        private int $spacingAfter = 16,
    ) {}

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

    /**
     * Returns the preferred gap after normally sized content
     */
    public function spacingAfter(): int
    {
        return $this->spacingAfter;
    }
}
