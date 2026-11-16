<?php

declare(strict_types=1);

namespace Lemonade\EmailGenerator\Presentation\Renderer;

use Lemonade\EmailGenerator\Component\Spacer;
use Lemonade\EmailGenerator\Contract\ComponentInterface;
use Lemonade\EmailGenerator\Exception\CompositionException;
use Lemonade\EmailGenerator\Exception\ErrorCode;
use Lemonade\EmailGenerator\Presentation\ComponentRenderer;
use Lemonade\EmailGenerator\Presentation\Contract\HtmlComponentRendererInterface;
use Lemonade\EmailGenerator\Presentation\RenderContext;
use Lemonade\EmailGenerator\Presentation\RenderedComponent;

/**
 * Renders Spacer content without its outer layout row
 *
 * @internal
 */
final readonly class SpacerRenderer implements HtmlComponentRendererInterface
{
    /**
     * Requests a fixed-height empty row without normal component spacing
     */
    public function render(
        ComponentInterface $component,
        RenderContext $context,
        int $contentWidth,
        ComponentRenderer $dispatcher,
    ): RenderedComponent {
        if (!$component instanceof Spacer) {
            throw new CompositionException(ErrorCode::ComponentUnsupported, 'SpacerRenderer requires Spacer.');
        }
        return new RenderedComponent('&nbsp;', $component->height());
    }
}
