<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Spacer;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Spacer content without its outer layout row
 *
 * @implements HtmlComponentRendererInterface<Spacer>
 *
 * @internal
 */
final readonly class SpacerRenderer implements HtmlComponentRendererInterface
{
    /**
     * Requests a fixed-height empty row without normal component spacing
     *
     * @param Spacer $component
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        ComponentRenderer $dispatcher,
        int $contentWidth,
    ): RenderedComponent {
        return new RenderedComponent('&nbsp;', $component->height());
    }
}
