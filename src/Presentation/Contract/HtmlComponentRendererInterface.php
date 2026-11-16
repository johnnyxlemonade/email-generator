<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Contract;

use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders one component's content separately from shared layout rows
 *
 * @internal
 */
interface HtmlComponentRendererInterface
{
    /**
     * Returns content and row sizing, or null to omit an invisible component
     *
     * Nested content uses the supplied dispatcher with its reduced available width
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): ?RenderedComponent;
}
